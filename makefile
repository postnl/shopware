#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help

PLUGIN_NAME=PostNLShopware
PLUGIN_VERSION=`php -r 'echo json_decode(file_get_contents("$(PLUGIN_NAME)/composer.json"))->version;'`

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ------------------------------------------------------------------------------------------------------------

install: ## Installs all production dependencies
	@composer install --no-dev
	@{ [ -d src/Resources/app/administration ] && cd src/Resources/app/administration || exit 0; } && { [ -f package.json ] && npm install --production || exit 0; }
	@{ [ -d src/Resources/app/storefront ] && cd src/Resources/app/storefront || exit 0; } && { [ -f package.json ] && npm install --production || exit 0; }

dev: ## Installs all dev dependencies
	@composer install
	@{ [ -d src/Resources/app/administration ] && cd src/Resources/app/administration || exit 0; } && { [ -f package.json ] && npm install || exit 0; }
	@{ [ -d src/Resources/app/storefront ] && cd src/Resources/app/storefront || exit 0; } && { [ -f package.json ] && npm install || exit 0; }

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

phpunit: ## Starts all Unit Tests
	@XDEBUG_MODE=coverage php vendor/bin/phpunit --configuration=phpunit.xml

infection: ## Starts all Infection/Mutation tests
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./.infection.json

infection-covered: ## Starts covered Infection/Mutation tests
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./.infection.json --only-covered

snippet-check: ## Tests and verifies all plugin snippets
	@php vendor/bin/phpunuhi validate --report-format=junit --report-output=./.reports/phpunuhi/junit.xml

snippet-export: ## Exports all snippets
	@php vendor/bin/phpunuhi export --dir=./.reports/phpunuhi

snippet-import: ## Imports the provided snippet set [set=xyz file=xz.csv]
	@php vendor/bin/phpunuhi import --set=$(set) --file=$(file) --intent=1

# ------------------------------------------------------------------------------------------------------------

pr: ## Prepares everything for a Pull Request
	@make dev
	@make csfix
	@make phpcheck -B
	@make phpmin -B
	@make phpstan -B
	@make snippet-check -B

build: ## Builds the package
	@rm -rf src/Resources/app/storefront/dist
	@mkdir -p src/Resources/app/storefront/dist
	@cd ../../.. && php bin/console plugin:refresh
	@cd ../../.. && php bin/console plugin:install $(PLUGIN_NAME) --activate --clearCache | true
	@cd ../../.. && php bin/console plugin:refresh
	@cd ../../.. && php bin/console theme:dump
	@cd ../../.. && php bin/console theme:refresh
	@cd ../../.. && PUPPETEER_SKIP_DOWNLOAD=1 ./bin/build-js.sh
	@cd ../../.. && php bin/console theme:refresh

release: ## Create a new release
	@make clean
	@make install
	@make build
	@make zip

zip: ## Creates a new ZIP package
	@php update-composer-require.php --shopware=">=6.5.2 <6.6" --env=prod --admin --storefront
	@cd .. && echo "Creating Zip file $(PLUGIN_NAME)-$(PLUGIN_VERSION).zip\n"
	@cd .. && rm -rf $(PLUGIN_NAME)-$(PLUGIN_VERSION).zip
	@cd .. && zip -qq -r -0 $(PLUGIN_NAME)-$(PLUGIN_VERSION).zip $(PLUGIN_NAME)/ -x@$(PLUGIN_NAME)/zip.exclude.lst
	@php update-composer-require.php --shopware=">=6.5.2 <6.6" --env=dev --admin --storefront
