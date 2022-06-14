#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help

PLUGIN_VERSION=`php -r 'echo json_decode(file_get_contents("PostNL/composer.json"))->version;'`

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ------------------------------------------------------------------------------------------------------------

install: ## Installs all production dependencies
	@composer install --no-dev
	@cd src/Resources/app/administration && npm install --production
	@cd src/Resources/app/storefront && npm install --production

dev: ## Installs all dev dependencies
	@composer install
	@cd src/Resources/app/administration && npm install
	@cd src/Resources/app/storefront && npm install

clean: ## Cleans all dependencies
	rm -rf vendor
	rm -rf .reports | true
	@make clean-node

clean-node: ## Removes node_modules
	rm -rf src/Resources/app/administration/node_modules
	rm -rf src/Resources/app/storefront/node_modules

# ------------------------------------------------------------------------------------------------------------

insights: ## Starts the PHPInsights Analyser
	@php vendor/bin/phpinsights analyse --no-interaction

csfix: ## Starts the PHP CS Fixer
	@php vendor/bin/php-cs-fixer fix --config=./.php_cs.php --dry-run

phpcheck: ## Starts the PHP syntax checks
	@find . -name '*.php' -not -path "./vendor/*" -not -path "./tests/*" | xargs -n 1 -P4 php -l

phpmin: ## Starts the PHP compatibility checks
	@php vendor/bin/phpcs -p --standard=PHPCompatibility --extensions=php --runtime-set testVersion 7.4 ./src

phpstan: ## Starts the PHPStan Analyser
	@php vendor/bin/phpstan analyse -c ./.phpstan.neon
	@php vendor/bin/phpstan analyse -c ./.phpstan.lvl8.neon

phpunit: ## Starts all Tests
	@XDEBUG_MODE=coverage php vendor/bin/phpunit --configuration=phpunit.xml --coverage-html ./.reports/postnl/coverage

infection: ## Starts all Infection/Mutation tests
	@phpdbg -qrr vendor/bin/infection --configuration=./.infection.json

# ------------------------------------------------------------------------------------------------------------

pr: ## Prepares everything for a Pull Request
	@php vendor/bin/php-cs-fixer fix --config=./.php_cs.php
	@make phpcheck -B
	@make phpmin -B
	@make phpstan -B

release: ## Creates a new ZIP package
	@cd .. && rm -rf PostNL-$(PLUGIN_VERSION).zip
	@cd .. && zip -qq -r -0 PostNL-$(PLUGIN_VERSION).zip PostNL/ -x '.editorconfig' '*.git*' '*.reports*' '*/tests*' '*/makefile' '*.DS_Store' '*/phpunit.xml' '*/.phpstan.neon' '*/.php_cs.php' '*/phpinsights.php' '*node_modules*' '*administration/build*' '*storefront/build*'
