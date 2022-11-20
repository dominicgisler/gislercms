.PHONY: default dependencies css
.DEFAULT_GOAL := default

default: css
	@docker-compose run --rm gislercms ./build.sh

dependencies: docker
	@docker-compose run --rm gislercms composer install

update-dependencies: docker
	@docker-compose run --rm gislercms composer update

css: docker
	@docker-compose run --rm gislercms ./build_css.sh

test:
	@docker-compose down
	@docker-compose up -d
	@-mv config/local.php config/local.php.bak
	@docker-compose run --rm gislercms vendor/bin/bdi detect drivers
	@-docker-compose run --rm gislercms ./vendor/bin/phpunit tests
	@-mv config/local.php.bak config/local.php

docker:
	@docker-compose build
