# Discus

A system that catalogs file listing of optical
data discs, Discus includes droplets for Windows and OS X
clients which allow the user to add an optical disc.
A wep app provides file and disc name retrieval.

![screenshot](http://discus-rhaleblian.c9users.io/README.png "Screen Shot")

The current incarnation of Discus uses http://c9.io
for development and end use.

## Server configuration
On a client, create

    mkdir $HOME/.config/yoyodyne
    cp template/media.json $HOME/.config/yoyodyne/media.json

and edit that template.

On the server, similarly 
    
    mkdir $HOME/.config/yoyodyne/media.ini
    cp template/media.ini $HOME/.config/yoyodyne/media.ini

Ray's Cloud9 workspace 'discus' does not
require this server setup.