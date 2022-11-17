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

docker:
	@docker-compose build
