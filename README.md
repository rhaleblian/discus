![The MIT License](http://img.shields.io/badge/license-MIT-blue.svg)

# discus

*discus* is a system that keeps catalogs of files on optical discs.

It includes droplet targets for Windows and OS X clients
which allow the user to drag and drop an optical disc to be catalogued.

A web app provides file and disc name retrieval.

![screenshot](https://github.com/rhaleblian/discus/blob/master/README.png)

## Configuration

### Web App

    ssh remotehost
    mkdir /etc/yoyodyne
    cp template/media.ini $HOME/.config/yoyodyne/media.ini
    make install-server
    
### Desktop Client

    mkdir -p $HOME/.config/yoyodyne
    cp template/media.json $HOME/.config/yoyodyne/media.json

and edit it.

#### Windows

Install ActiveState Python 2.7, including the Win32 extensions, to `C:\Python27`.

Clone and install:

    cd /cygdrive/d/Developer
    git clone https://github.com/rhaleblian/discus.git
    make install-client
    
Create a Desktop shortcut to `media-add.bat`.

#### OS X

Install pymysql.

Clone and install:

    cd ~/Developer
    git clone https://github.com/rhaleblian/discus.git
    make install-client
    
Copy the Automator droplet `Add to Media Catalog.app` to the Desktop:

    cp -r 'platform/osx/Automator/Add to Media Catalog.app' ~/Desktop
