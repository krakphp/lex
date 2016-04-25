PERIDOT = ./vendor/bin/peridot

.PHONY: test

test:
	$(PERIDOT) -g '*.php' test
