#!/usr/bin/env python
import os
import re
import subprocess
import sys
#import urllib2

#wget = 'wget'
wget = r'"c:\Program Files (x86)\GnuWin32\bin\wget"'
prog, url = sys.argv
print prog, url

ext = os.path.splitext(url)[1]
#print ext
if ext == '.url':
    fp = open(url)
    lines = fp.readlines()
    #print lines
    m = re.match('URL=(.+)$', lines[1].strip())
    url = m.group(1)
    #print m.group(1)
    fp.close()

dirname, basename = os.path.split(url)
outdir = os.path.split(dirname)[1]
m = re.match('(.+)([0-9]+)(\.[a-z][a-z][a-z])$', basename)
if not m:
    print dirname, basename, outdir, 'fail'
    sys.exit(0)

digits = m.group(2)
pad = len(digits)
expr = '0%dd' % pad
expr = '%' + expr
expr = m.group(1) + expr + m.group(3)
for i in range(1, 13):
    url = '/'.join([dirname, expr % i])
    prefix, name = os.path.split(url)
    print url, outdir, name

    cmd = '%s -P %s %s' % (wget, outdir, url)
#    cmd = '%s %s' % (wget, url)
    subprocess.call(cmd)

    # req = urllib2.Request(url)
    # response = urllib2.urlopen(url)
    # fp = open(name, 'w')
    # fp.write(response.read())
    # fp.close()
