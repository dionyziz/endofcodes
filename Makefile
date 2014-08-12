all: syntax test

syntax:
	for i in `find .|grep '\.php$$'`; do php -l $$i || exit 2; done

test:
	for i in `find tests -type f|sed 's/^tests\///'|sed 's/\.php$$//'`; do php run testrun create name=$$i || exit 1; done
