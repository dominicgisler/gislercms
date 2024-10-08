.PHONY: default release dependencies update-dependencies css test docker dev-latest
.DEFAULT_GOAL := default

default: css
	@docker-compose run --rm gislercms ./build.sh

release: css
	@if [ "$(version)" = "" ]; then \
	    echo "please specify version (make release version=v1.0.0)"; \
	    exit 1; \
	fi
	@sed -i 's/dev-latest/$(version)/g' config/default.php
	@docker-compose run --rm gislercms ./build.sh
	@sed -i 's/$(version)/dev-latest/g' config/default.php

dependencies: docker
	@docker-compose run --rm gislercms composer install

update-dependencies: docker
	@docker-compose run --rm gislercms composer update

css: docker
	@docker-compose run --rm gislercms ./build_css.sh

test:
	@docker-compose down
	@docker-compose up -d
	@sleep 10
	@-mv config/local.php config/local.php.bak
	@docker-compose run --rm gislercms vendor/bin/bdi detect drivers
	@-docker-compose run --rm gislercms ./vendor/bin/phpunit --testdox tests
	@rm -rf config/local.php
	@-mv config/local.php.bak config/local.php

docker:
	@docker-compose build

dev-latest: default
	@if [ "$$(command -v gh)" = "" ]; then echo "github-cli is needed for this action!" && exit 1; else exit 0; fi
	-gh release delete dev-latest --cleanup-tag -y
	@gh release create dev-latest --generate-notes --latest=false --prerelease --target dev gislercms.zip
