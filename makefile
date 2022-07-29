#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help

PLUGIN_VERSION=`php -r 'echo json_decode(file_get_contents("PostNLShopware/composer.json"))->version;'`

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
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./.infection.json

# ------------------------------------------------------------------------------------------------------------

pr: ## Prepares everything for a Pull Request
	@make dev
	@php vendor/bin/php-cs-fixer fix --config=./.php_cs.php
	@make phpcheck -B
	@make phpmin -B
	@make phpstan -B

build: ## Builds the package
	@rm -rf src/Resources/app/storefront/dist
	@cd ../../.. && php bin/console plugin:refresh
	@cd ../../.. && php bin/console plugin:install PostNLShopware --activate --clearCache | true
	@cd ../../.. && php bin/console plugin:refresh
	@cd ../../.. && php bin/console theme:dump
	@cd ../../.. && PUPPETEER_SKIP_DOWNLOAD=1 ./bin/build-js.sh
	@cd ../../.. && php bin/console theme:refresh
	@cd ../../.. && php bin/console theme:compile
	@cd ../../.. && php bin/console theme:refresh

release: ## Create a new release
	@make clean
	@make install
	@make build
	@make zip

zip: ## Creates a new ZIP package
	@php update-composer-require.php --shopware=^6.4.1 --env=prod
	@cd .. && echo "\nCreating Zip file PostNLShopware-$(PLUGIN_VERSION).zip\n"
	@cd .. && rm -rf PostNLShopware-$(PLUGIN_VERSION).zip
	@cd .. && zip -qq -r -0 PostNLShopware-$(PLUGIN_VERSION).zip PostNLShopware/ -x '*.editorconfig' '*.git*' '*.reports*' '*/tests*' '*/makefile' '*.DS_Store' '*/phpunit.xml' '*/.phpstan.neon' '*/.php_cs.php' '*/phpinsights.php' '*node_modules*' '*administration/build*' '*storefront/build*' '*/update-composer-require.php'
	@php update-composer-require.php --shopware=^6.4.1 --env=dev
