PREFIX=$(HOME)
PYTHON_SITE=$(PREFIX)/lib/python/site-packages
include Makefile.$(shell uname)

install: install-client install-server
uninstall: uninstall-client

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYTHON_SITE)/yoyodyne
	install yoyodyne/__init__.py $(PYTHON_SITE)/yoyodyne
	install yoyodyne/media.py $(PYTHON_SITE)/yoyodyne

install-server:
	scp index.php halebs@haleblian.com:public_html/media/index.php

uninstall-client:
	- rm $(PREFIX)/bin/media
	- rm $(PYTHON_SITE)/yoyodyne/media.py[c]
	- rm $(PYTHON_SITE)/yoyodyne/__init__.py[c]
	- rmdir $(PYTHON_SITE)/yoyodyne
