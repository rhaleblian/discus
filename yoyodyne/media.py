#!/usr/bin/env python
"""
    Operations on optical media catalog.
    Called by shell script.
"""
import json, os, time, re, platform
import logging
import sqlalchemy
import tabulate

if platform.system() == 'Windows':
    import win32api  # pylint: disable=import-error

logging.basicConfig()

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


def engine():
    conf = json.load(open(config_path()))
    return sqlalchemy.create_engine(conf['url'])


def connect():
    return engine().connect()


def add(disc, label=None):
    """ Add disc contents to index. """
    disc = re.sub('/$', '', disc)

    if platform.system() == 'Windows':
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

    print 'Volume Label:', disc, 'Printed Label:', label, 'Path:', path
    if not os.path.exists(path):
        logging.error('fatal: cannot find path.')
        return

    cursor = connect()

    if label and len(label):
        labelexpr = "'%s'" % escape(label)
    else:
        labelexpr = 'NULL'

    # Define the new disc.

    sql = """insert into disc (name, label, format, status)
values ('%s', %s, NULL, 0);""" % (escape(disc), labelexpr)
    rows = cursor.execute(sqlalchemy.text(sql))
    if rows == 0:
        print 'warning: no rows inserted'
    sql = 'select id from disc order by id desc limit 1'
    result = cursor.execute(sqlalchemy.text(sql))
    iid = result.fetchone()[0]

    # Insert paths in chunks of 128 statements.

    chunksize = 128
    walkroot = path
    sql = ""
    count = 0
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
            count += 1
            if count > chunksize:
                cursor.execute(sqlalchemy.text(sql))
                count = 0
                sql = ""

    if len(sql):
        cursor.execute(sqlalchemy.text(sql))

def eject():
    raise NotImplementedError

def files(disc_id):
    """ return: disc ID, dir, filename """
    cursor = connect()
    metadata = sqlalchemy.schema.MetaData(bind=cursor)
    table = sqlalchemy.Table('file', metadata, autoload=True)

    select = sqlalchemy.sql.expression.select
    statement = select([table.c.dir, table.c.name]).where(table.c.disc_id == disc_id).order_by(table.c.dir)
    return cursor.execute(statement).fetchall()

def discs():
    """ return: disc names and IDs """
    cursor = connect()
    metadata = sqlalchemy.schema.MetaData(bind=cursor)
    table = sqlalchemy.Table('disc', metadata, autoload=True)
    
    select = sqlalchemy.sql.expression.select
    statement = select([table.c.id, table.c.name, table.c.label]).order_by(table.c.id)
    return cursor.execute(statement).fetchall()

def search(term, field='file'):
    """ Return rows in index containing pattern :term:. """

    # TODO rewrite for sqlalchemy
    sql = """select disc.label,file.dir,file.name from disc
             inner join file on disc.id = file.disc_id where """
    if field == 'dir':
        sql += "dir.name"
    elif field == 'disc':
        sql += "disc.label"
    elif field == 'file':
        sql += 'file.name'
    sql += " like '%%%s%%'" % term
    sql += " order by disc.label, file.dir, file.name"
    sql += ";"

    cursor = connect()
    return cursor.execute(sqlalchemy.text(sql))

def display(selection):
    print(tabulate.tabulate(selection))