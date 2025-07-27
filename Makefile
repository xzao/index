#
#	Makefile
#
.DEFAULT_GOAL := logs


#
#	dir[s]
#
$(shell mkdir -p var)


#
#	env[s]
#
$(shell test -f .env || cp .env.sample .env)
include .env
export $(shell sed 's/=.*//' .env)


#
#	target[s]
#
logs:
	docker-compose logs -f

restart:
	make stop
	make start

shell:
	docker exec -it services-index bash

start:
	docker-compose up -d --build

stop:
	docker-compose down
