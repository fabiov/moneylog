include .env

bold=`tput bold`
green=`tput setaf 2`
reset=`tput sgr0`

help:
	@echo "${bold}Docker containers manipulation commands${reset}"
	@echo "${green}up${reset}	Start containers in background mode"
	@echo "${green}down${reset}	Stops containers and removes containers, networks, volumes, and images"
	@echo ""
	@echo "${bold}Interactive terminals${reset}"
	@echo "${green}sh${reset}	Web container shell"
	@echo "${green}mysql${reset}	MySQL terminal"
	@echo ""
	@echo "${bold}Miscellaneous Macro${reset}"
	@echo "${green}help${reset}	Show this message"
	@echo "${green}setup${reset}	Initialize development environment"
	@echo ""

# Docker containers manipulation commands ##############################################################################
up:
	@docker-compose up -d

ps:
	@docker-compose ps

down:
	@docker-compose down

# Interactive Terminals ################################################################################################
sh console:
	@docker-compose exec web bash

mysql:
	@docker-compose exec db mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME}

# Miscellaneous ########################################################################################################

setup: up
	@docker-compose exec web composer install
	@cp config/autoload/local.php.dist config/autoload/local.php
	@mkdir -p data/DoctrineORMModule/Proxy
	@chmod a+w data/DoctrineORMModule/Proxy
	@docker-compose exec web vendor/bin/doctrine-module orm:schema-tool:update --force
	@git config core.hooksPath .githooks
