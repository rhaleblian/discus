PREFIX=/usr/local
include Makefile.$(shell uname)

default:
	@echo "rules: install"

install:
	install media $(PREFIX)/bin
	install __init__.py $(PREFIX)/lib/python2.6/site-packages/twiddlets
	install media.py $(PREFIX)/lib/python2.6/site-packages/twiddlets

install-server:
	install index.php $(WEBROOT)
