PREFIX=$(HOME)
PYTHON_SITE=$(PREFIX)/lib/python/site-packages
include Makefile.$(shell uname)

install: install-client install-server
uninstall: uninstall-client

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYTHON_SITE)/twiddlets
	install twiddlets/__init__.py $(PYTHON_SITE)/twiddlets
	install twiddlets/media.py $(PYTHON_SITE)/twiddlets

install-server:
	scp index.php halebs@haleblian.com:public_html/media/index.php

uninstall-client:
	- rm $(PREFIX)/bin/media
	- rm $(PYTHON_SITE)/twiddlets/media.py
	- rm $(PYTHON_SITE)/twiddlets/__init__.py
	- rmdir $(PYTHON_SITE)/twiddlets
