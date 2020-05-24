PREFIX=$(HOME)/.local
PYTHON_SITE=$(PREFIX)/lib/python/site-packages
DESTDIR=/var/www/discus

include platform/Makefile.$(shell uname)

default:

install:
	python setup.py install

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYTHON_SITE)/yoyodyne
	install yoyodyne/__init__.py $(PYTHON_SITE)/yoyodyne
	install yoyodyne/media.py $(PYTHON_SITE)/yoyodyne

install-server:
	install -d $(DESTDIR)
	install favicon.png $(DESTDIR)
	install index.php $(DESTDIR)
	install model.php $(DESTDIR)

uninstall-client:
	- rm $(PREFIX)/bin/media
	- rm $(PYTHON_SITE)/yoyodyne/media.py[c]
	- rm $(PYTHON_SITE)/yoyodyne/__init__.py[c]
	- rmdir $(PYTHON_SITE)/yoyodyne

uninstall-server:
	- rm $(DESTDIR)/index.php
	- rm $(DESTDIR)/model.php
	- rm $(DESTDIR)/favicon.png
	- rmdir $(DESTDIR)
