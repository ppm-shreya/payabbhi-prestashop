VERSION_FILE=VERSION
VER=`cat $(VERSION_FILE)`

release: init install prepare archive cleanup

init:
	mkdir dist

install:
	composer require payabbhi/payabbhi-php
	mv vendor payabbhi/

prepare:
	cp VERSION payabbhi
	markdown-pdf README.md
	zip -r payabbhi.zip payabbhi

archive:
	zip -r payabbhi-prestashop-$(VER).zip payabbhi.zip README.pdf
	tar -cvzf payabbhi-prestashop-$(VER).tar.gz payabbhi.zip README.pdf

cleanup:
	mv payabbhi-prestashop-$(VER).zip dist
	mv payabbhi-prestashop-$(VER).tar.gz dist
	rm payabbhi/VERSION
	rm composer.*
	rm -rf payabbhi/vendor
	rm README.pdf
	rm payabbhi.zip

clean:
	rm -rf dist
