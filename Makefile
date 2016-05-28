PREFIX=$(HOME)
PYTHON_SITE=$(PREFIX)/lib/python/site-packages
DESTSSH=rhaleblian@discus
DESTDIR=$(DESTSSH):/var/www/html/discus

include platform/Makefile.$(shell uname)

default:

install-client:
	install -d $(PREFIX)/bin
	install media $(PREFIX)/bin
	install -d $(PYTHON_SITE)/yoyodyne
	install yoyodyne/__init__.py $(PYTHON_SITE)/yoyodyne
	install yoyodyne/media.py $(PYTHON_SITE)/yoyodyne

install-server:
	scp favicon.png $(DESTDIR)
	scp index.php $(DESTDIR)
	scp model.php $(DESTDIR)

uninstall-client:
	- rm $(PREFIX)/bin/media
	- rm $(PYTHON_SITE)/yoyodyne/media.py[c]
	- rm $(PYTHON_SITE)/yoyodyne/__init__.py[c]
	- rmdir $(PYTHON_SITE)/yoyodyne

uninstall-server:
	ssh $(DESTSSH) rm -r public_html/ray/discus
