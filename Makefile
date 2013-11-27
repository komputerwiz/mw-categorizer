SF = php app/console
SFFLAGS = --env=prod --no-debug


.PHONY: default
default:
	@echo "Usage: make install|update"

composer.phar:
	curl -s getcomposer.org/installer | php

.PHONY: install
install: composer.phar
	chmod -R 777 app/logs app/cache
	bower install
	php composer.phar install
	$(SF) $(SFFLAGS) assetic:dump


.PHONY: update
update: composer.phar
	php composer.phar self_update
	git pull
	bower install
	php composer.phar install
	$(SF) $(SFFLAGS) cache:clear
	$(SF) $(SFFLAGS) assetic:dump
	$(SF) $(SFFLAGS) assets:install --symlink --relative web
