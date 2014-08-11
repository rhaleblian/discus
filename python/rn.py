#!/usr/bin/env python
import os
import random
import sys
prefix = str(int(random.random() * 1000))
for f in sys.argv[1:]:
    d, ff = os.path.split(f)
    ff = str(prefix) + '-' + ff
    ff = os.path.join(d, ff)
    os.rename(f, ff)
