PREFIX=$(HOME)/usr/local
include Makefile.$(shell uname)

default:
	@echo "rules: install"

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYPREFIX)/site-packages/twiddlets
	install twiddlets/__init__.py $(PYPREFIX)/site-packages/twiddlets
	install twiddlets/media.py $(PYPREFIX)/site-packages/twiddlets

install-server:
	scp index.php halebs@haleblian.com:public_html/media/index.php

install: install-client install-server

uninstall-client:
	rm $(PREFIX)/bin/media	
	rm $(PYPREFIX)/site-packages/twiddlets/media.py
	rm $(PYPREFIX)/site-packages/twiddlets/__init__.py
	rmdir $(PYPREFIX)/site-packages/twiddlets

uninstall: uninstall-client
