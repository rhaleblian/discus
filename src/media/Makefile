PREFIX=/usr/local
WEBROOT=//mustard/www/media
#WEBROOT=/Volumes/www

include Makefile.$(shell uname)

default:
	@echo "rules: install"

install:
	install media $(PREFIX)/bin
	install index.php $(WEBROOT)
	install style.php $(WEBROOT)

