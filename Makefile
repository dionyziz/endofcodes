all:
	for i in `find .|grep '\.php$$'`; do php -l $$i || exit 2; done
	php run testrun create all=yes
