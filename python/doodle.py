#!/usr/bin/env python
import re
import sys
import os

prog, url = sys.argv
dirname, basename = os.path.split(url)
outdir = os.path.split(dirname)[1]
m = re.match('(.+)([0-9]+)(\.[a-z][a-z][a-z])$', basename)
if not m:
    exit

digits = m.group(2)
pad = len(digits)
expr = '0%dd' % pad
expr = '%' + expr
expr = m.group(1) + expr + m.group(3)
for i in range(1,13):
    url = os.path.join(dirname, expr % i)
    os.system('wget -P %s %s' % (outdir,url))
