# Discus

A system that catalogs file listing of optical
data discs, Discus includes droplets for Windows and OS X
clients which allow the user to add an optical disc.
A wep app provides file and disc name retrieval.

![screenshot](http://discus-rhaleblian.c9users.io/README.png "Screen Shot")

The current incarnation of Discus uses http://c9.io
aka Cloud9 for development and end use.

## Client configuration

    mkdir $HOME/.config/yoyodyne
    cp template/media.json $HOME/.config/yoyodyne/media.json

and edit it.

On Windows, create a Desktop shortcut to `media-add.bat`. 
On OS X, copy the Automator droplet `Add to Media Catalog.app`
to the Desktop. Package `yoyodyne` needs to be in Python's
module namespace.

## Server configuration

    mkdir $HOME/.config/yoyodyne/media.ini
    cp template/media.ini $HOME/.config/yoyodyne/media.ini

and edit it.
Ray's Cloud9 workspace 'discus' does not
require this server setup.