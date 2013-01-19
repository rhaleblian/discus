#!/usr/bin/python
"""
    Operations on optical media catalog.
    Shell script.
"""
import json, os, sys, time, re, platform
from stat import *
import pymysql

def escape(expr):
    return re.sub('\'','\\\'',expr)

def month_as_integer(abbrev):
    ab = ('Jan','Feb','Mar','Apr','May','Jun',
          'Jul','Aug','Sep','Oct','Nov','Dec')
    for i in range(0,11):
        if ab[i] == abbrev:
            return i+1
    return None
    
def connect():
    confpath = os.path.join(os.getenv('HOME'), '.config', 'media.json')
    conf = json.load(open(confpath))
    return pymysql.connect(conf['host'], conf['user'], conf['passwd'], conf['db'])

def add(args, label=None, debug=True):
    """ Add disc contents to index. """
    (_unused, disc) = args
    disc = re.sub('/$', '', disc)
    if re.match('CYGWIN.+', platform.system()):
        mountpoint = '/cygdrive/d'
        path = mountpoint
    elif platform.system() == 'Windows':
        mountpoint = 'd:/'
        path = mountpoint
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
    else:
        path = disc
        disc = os.path.split(path)[1]
  
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
    iid = cursor.lastrowid

    walkroot = path
    sql = ""
    for root, dirs, files in os.walk(walkroot):
        for file in files:
            try:
                st = os.stat(root + "/" + file)
            except:
                pass
            
            root = re.sub(path, '', root)
            t = time.strftime("%Y-%m-%d",
                              time.localtime(st.st_mtime))
            sql += """insert into file
                (name,dir,disc_id,bytes,mtime)
                values ('%s','%s',%d,'%s','%s');
                """ % (escape(file),
                       escape(root),
                       iid,
                       st.st_size,
                       t)
    rows = cursor.execute(sql)

def search(args):
    """ Return rows in index containing pattern. """
    field = 'file'
    pattern = '%'
    if len(args) == 1:
        (pattern,) = args
    elif len(args) == 2:
        (field, pattern) = args
    else:
        return

    cmd  = """select disc.label,file.dir,file.name from disc
        inner join file on disc.id = file.disc_id where """
    if field == 'dir':
        cmd += "dir.name"
    elif field == 'disc':
        cmd += "disc.label"
    else:
        cmd += "file.name"
        cmd += " like '%%%s%%';" % pattern
	print cmd

    connection = connect()
    cursor = connection.cursor()
    rows = cursor.execute(cmd)
    if not rows:
        return
    rows = cursor.fetchall()
    for row in rows:
        print row

def upgrade(args):
    (disc_id) = args
    connection = connect()
    cursor = connection.cursor()

    rows = cursor.execute("""
select label,name from disc where id = %s""", (disc_id))
    if not rows:
        return
    rows = cursor.fetchone()
    disclabel, discname = rows
    if not discname:
        discname = disclabel

    rows = cursor.execute("""
select id,disc_id,dir,name from file
where disc_id = %s;""", (disc_id))
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
            cursor.execute("""
update file set dir = %s where id = %s;""", (dnew, idd))

