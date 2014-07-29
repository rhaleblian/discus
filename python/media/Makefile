PREFIX=/usr/local
include Makefile.$(shell uname)

default:
	@echo "rules: install"

install: install-client install-server
uninstall: uninstall-client

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYPREFIX)/site-packages/twiddlets
	install __init__.py $(PYPREFIX)/site-packages/twiddlets
	install media.py $(PYPREFIX)/site-packages/twiddlets

install-server:
	scp index.php haleblia@host233.hostmonster.com:public_html/ray/media/index.php

uninstall-client:
	rm $(PREFIX)/bin/media	
	rm $(PYPREFIX)/site-packages/twiddlets/media.py
	rm $(PYPREFIX)/site-packages/twiddlets/__init__.py
    rmdir $(PYPREFIX)/site-packages/twiddlets
