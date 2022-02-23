"""
    Operations on optical media catalog.
    Called by shell script.
"""
import argparse
import json
import logging
import os
import time
import re
import platform
from typing import Sequence
import sqlalchemy
import sqlalchemy.orm

Base = sqlalchemy.orm.declarative_base()


class File(Base):
    __tablename__ = 'file'
    id = sqlalchemy.Column(sqlalchemy.Integer, sqlalchemy.Sequence('file_id_seq'), primary_key=True)
    disc_id = sqlalchemy.Column(sqlalchemy.Integer)
    name = sqlalchemy.Column(sqlalchemy.String(128))
    dir = sqlalchemy.Column(sqlalchemy.String(256))
    mtime = sqlalchemy.Column(sqlalchemy.DateTime)
    bytes = sqlalchemy.Column(sqlalchemy.Integer)


class Disc(Base):
    __tablename__ = 'disc'
    id = sqlalchemy.Column(sqlalchemy.Integer, sqlalchemy.Sequence('disc_id_seq'), primary_key=True)
    name = sqlalchemy.Column(sqlalchemy.String(32))
    label = sqlalchemy.Column(sqlalchemy.String(64))
    status = sqlalchemy.Column(sqlalchemy.Integer)


#Session = sqlalchemy.orm.sessionmaker()


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


def create_engine():
    conf = json.load(open(config_path()))
    engine = sqlalchemy.create_engine(conf['url'])
    return engine


def connect():
    engine = create_engine()
    # Session.configure(bind=engine)
    return engine.connect()


def create():
    Base.metadata.create_all(connect())


def add(disc, label=None):
    """ Add disc contents. """
    engine = create_engine()
    with sqlalchemy.orm.Session(engine) as session:

        # Infer disc volume label from path.

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
        if not label:
            label = disc

        print('= volume label:', disc)
        print('= printed label:', label)
        print('= mounted path:', path)

        if not os.path.exists(path):
            logging.error('fatal: cannot find mounted path.')
            return

        # Define the new disc.

        row = Disc(name=disc, label=label, status=0)
        session.add(row)
        # Commit now so we can fetch the id.
        session.commit()

        # Get the disc id.

        sql = 'select id from disc order by id desc limit 1'
        connection = engine.connect()
        result = connection.execute(sqlalchemy.text(sql))
        iid = result.fetchone()[0]
        print('= disc id', iid)

        # Insert files for this disc.

        count = 0
        # Don't let the transaction get too large.
        chunksize = 256
        for root, dirs, files in os.walk(path):
            dirname = re.sub(path, '', root)
            print('= visiting', dirname)
            for filename in files:
                statpath = os.path.join(root, filename)
                try:
                    stats = os.stat(statpath)
                    bytes = stats.st_size
                    mtime = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(stats.st_mtime))
                except Exception as e:
                    print('! could not stat "{0}"'.format(statpath))
                    bytes = None
                    mtime = None
                row = File(name=filename, dir=dirname, disc_id=iid, bytes=stats.st_mtime, mtime=mtime)
                session.add(row)
            count += 1
            if count >= chunksize:
                print('= committing progress...')
                count = 0
                session.commit()
        print('= performing final commit.')
        session.commit()


def list_discs():
    engine = create_engine()
    with sqlalchemy.orm.Session(engine) as session:
        for row in session.query(Disc).all():
            print(row.id, row.name, row.label, row.status)


def search(term, field='file'):
    """ Return rows in index containing :term:. """
    engine = create_engine()
    with sqlalchemy.orm.Session(engine) as session:
        for row in session.query(File).filter(File.name.like('%{0}%'.format(term))):
            disc = session.query(Disc).filter(Disc.id == row.disc_id).first()
            print(disc.name, '"{0}"'.format(os.path.join(row.dir, row.name)))


def command_add(args):
    add(args.path, args.label)


def command_help(args):
    print('try -h.')


def command_list(args):
    list_discs()


def command_search(args):
    search(args.term, field=args.field)


def main():
    parser = argparse.ArgumentParser(description="""
    Operations on the optical media library.
    """)
    subparsers = parser.add_subparsers(title='subcommands')
    parser.set_defaults(func=command_help)

    parser_add = subparsers.add_parser('add')
    parser_add.add_argument('--label',
                            help='ignore volume label on media, use LABEL')
    parser_add.add_argument('path',
                            help='path to media (name when dropped on shortcut)')
    parser_add.set_defaults(func=command_add)

    parser_search = subparsers.add_parser('search')
    parser_search.add_argument('--field', default='file')
    parser_search.add_argument('term',
                               help='search file and folder names for this term')
    parser_search.set_defaults(func=command_search)

    parser_eject = subparsers.add_parser('list')
    parser_eject.set_defaults(func=command_list)

    args = parser.parse_args()
    args.func(args)
