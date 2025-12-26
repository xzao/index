#
#	Makefile
#
.DEFAULT_GOAL := logs


#
#	env[s]
#
$(shell test -f .env || cp .env.sample .env)
include .env
export $(shell sed 's/=.*//' .env)


#
#	target[s]
#
build:
	@docker-compose build

develop:
	@php -S 0.0.0.0:8000 -t public

install:
	ln -sf $(shell pwd)/mnt/etc/index /etc/index
	@if [ ! -d 'public/vendor/selfhst-icons' ]; then \
		cd public/vendor && git clone --depth 1 --single-branch https://github.com/selfhst/icons selfhst-icons; \
	fi

logs:
	@docker-compose logs -f

restart:
	make stop
	make start

shell:
	@docker exec -it ${INDEX_CONTAINER_NAME} bash

start:
	@docker-compose up -d --build

stop:
	@docker-compose down

upgrade:
	@docker-compose up -d --build --force-recreate
