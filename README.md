# discus

*discus* is a system that keeps catalogs of files on optical discs.

It includes droplet targets for Windows and OS X clients
which allow the user to drag and drop an optical disc to be catalogued.

A web app provides file and disc name retrieval.

![screenshot](http://discus-rhaleblian.c9users.io/README.png "Screen Shot")

## Configuration

### Web App

    ssh remotehost
    mkdir $HOME/.config/yoyodyne/media.ini
    cp template/media.ini $HOME/.config/yoyodyne/media.ini

### Client

    mkdir $HOME/.config/yoyodyne
    cp template/media.json $HOME/.config/yoyodyne/media.json

and edit it.

#### Windows

    cd /cygdrive/d/Developer
    git clone ...
    
2. Install ActiveState Python 2.7, including the Win32 extensions, to `C:\Python27`.
3. Clone to D:\Developer\discus.
4. Create a Desktop shortcut to `media-add.bat`.

#### OS X

1. Copy the Automator droplet `Add to Media Catalog.app` to the Desktop.
2. Package `yoyodyne` needs to be in Python's module namespace.
