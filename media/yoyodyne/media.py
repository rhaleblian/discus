#!/usr/bin/env python
"""
    Operations on optical media catalog.
    Called by shell script.
"""
import json, os, time, re, platform
import pymysql


def escape(expr):
    """
    :param expr: str
    :return: str, backslash-escaped expr
    """
    return re.sub('\'', '\\\'', expr)


def month_as_integer(abbrev):
    """
    :param abbrev: Jan, etc
    :return: int, month number
    """
    abbrevs = ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
               'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')
    for i in range(0, 11):
        if abbrevs[i] == abbrev:
            return i+1
    return None


def config_path():
    if platform.system() == 'Windows':
        return os.path.join(os.getenv('USERPROFILE'), '.config', 'yoyodyne', 'media.json')
    else:
        return os.path.join(os.getenv('HOME'), '.config', 'yoyodyne', 'media.json')


def connect():
    conf = json.load(open(config_path()))
    return pymysql.connect(conf['host'],
                           conf['username'],
                           conf['password'],
                           conf['db'])


def add(disc, label=None):
    """ Add disc contents to index. """
    disc = re.sub('/$', '', disc)
    if platform.system() == 'Windows':
        import win32api
        mountpoint = os.path.split(disc)[0]
        mountpoint = re.sub(r'\\', '', mountpoint) 
        path = mountpoint
        volinfo = win32api.GetVolumeInformation(mountpoint)
        if volinfo:
            disc = volinfo[0]
            #disc_format = volinfo[4]
            if not label:
                label = disc
    elif platform.system() == 'Darwin':
        # Allow passing the disc name
        # or a path to the mountpoint.
        path = '/Volumes'
        if not re.match('/', disc):
            # Automator passes a disc name as an
            # classic-style Volume designator.
            disc = re.sub(':$', '', disc)
            path = os.path.join(path, disc)
        else:
            path = disc
            disc = os.path.split(path)[1]
    elif re.match('CYGWIN.+', platform.system()):
        mountpoint = '/cygdrive/d'
        path = mountpoint
    else:
        path = disc
        disc = os.path.split(path)[1]

    print 'Volume:', disc, 'Writing:', label, 'Path:', path
    if not os.path.exists(path):
        return

    connection = connect()
    cursor = connection.cursor()
    if label and len(label):
        labelexpr = "'%s'" % escape(label)
    else:
        labelexpr = 'NULL'
    sql = """insert into disc (name, label, format, status)
values ('%s', %s, NULL, 0);""" % (escape(disc), labelexpr)
    rows = cursor.execute(sql)
    if rows == 0:
        print 'warning: no rows inserted'
    iid = cursor.lastrowid

    walkroot = path
    sql = ""
    for root, _unused, files in os.walk(walkroot):
        root = re.sub('\\\\', '/', root)
        print root
        for name in files:
            try:
                stats = os.stat(root + '/' + name)
            except OSError:
                pass
            
            root = re.sub(path, '', root)
            t_mod = time.strftime("%Y-%m-%d",
                                  time.localtime(stats.st_mtime))
            sql += """insert into file
                (name,dir,disc_id,bytes,mtime)
                values ('%s','%s',%d,'%s','%s');
                """ % (escape(name),
                       escape(root),
                       iid,
                       stats.st_size,
                       t_mod)
    rows = cursor.execute(sql)


def search(args):
    """ Return rows in index containing pattern. """
    field = 'file'
    pattern = '%'
    if len(args) == 1:
        (pattern,) = args
    elif len(args) == 2:
        (field, pattern) = args

    sql = """select disc.label,file.dir,file.name from disc
             inner join file on disc.id = file.disc_id where """
    if field == 'dir':
        sql += "dir.name"
    elif field == 'disc':
        sql += "disc.label"
    else:
        sql += 'file.name'
        sql += " like '%%%s%%';" % pattern

    connection = connect()
    cursor = connection.cursor()
    rows = cursor.execute(sql)
    if not rows:
        return
    rows = cursor.fetchall()
    for row in rows:
        print row


def upgrade(args):
    (disc_id) = args
    connection = connect()
    cursor = connection.cursor()

    rows = cursor.execute('select label,name from disc '
                          'where id = %s',
                          disc_id)
    if not rows:
        return
    rows = cursor.fetchone()
    # The label is printed on the surface,
    # the name is the filesystem's volume label.
    disclabel, discname = rows
    if not discname:
        discname = disclabel

    rows = cursor.execute('select id,disc_id,dir,name from file '
                          'where disc_id = %s;',
                          disc_id)
    if not rows:
        return
    rows = cursor.fetchall()
    for row in rows:
        idd, disc_idd, d, n = row
        if not d:
            continue
        dnew = d
        if re.match('^\.', d):
            dnew = '/'
        elif re.match('^\./', d):
            dnew = re.sub('^\./', '/', d)
        elif re.match('^/Volumes/%s' % discname, d):
            dnew = re.sub('^/Volumes/%s' % discname, '', d)
        dnew = re.sub("^'", "", dnew)
        dnew = re.sub("'$", "", dnew)
        print idd, disc_idd, dnew, n
        if dnew != d:
            cursor.execute('update file set dir = %s '
                           'where id = %s;',
                           (dnew, idd))
