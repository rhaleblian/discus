# discus

*discus* is a system that keeps catalogs of files on optical discs.

It includes droplet targets for Windows and OS X clients
which allow the user to drag and drop an optical disc to be catalogued.

A web app provides file and disc name retrieval.

![screenshot](http://discus-rhaleblian.c9users.io/README.png "Screen Shot")

The current incarnation of Discus uses http://c9.io
aka Cloud9 for development.

## Server configuration

    ssh remotehost
    mkdir $HOME/.config/yoyodyne/media.ini
    cp template/media.ini $HOME/.config/yoyodyne/media.ini

## Client configuration

    mkdir $HOME/.config/yoyodyne
    cp template/media.json $HOME/.config/yoyodyne/media.json

and edit it.

### Windows

Install ActiveState Python 2.7, including the Win32 extensions, to `C:\Python27`.
Clone to D:\Developer\discus.
Create a Desktop shortcut to `media-add.bat`.

### OS X

Copy the Automator droplet `Add to Media Catalog.app` to the Desktop.
Package `yoyodyne` needs to be in Python's module namespace.

and edit it.
Ray's Cloud9 workspace `discus` does not
require this server setup.
