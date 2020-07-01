IMAGE_PREFIX		= myminio-
CONTAINER_PREFIX	= myminio-
VOLUME_PREFIX		= docker_myminio_
DEVELOPER_UID		?= $(shell id -u)

DOCKER				= docker
DOCKER_COMPOSE		= docker-compose
.DEFAULT_GOAL		:= help

BOLD = \033[1m
NORMAL = \033[0m

%::
	@echo Unknown target: \`$@\`
	@$(MAKE) -s

help: ## Outputs this help screen
	@echo -e '${BOLD}make [TARGET]${NORMAL}'
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
	@echo
	@$(MAKE) -s env-info

env-info:
	@echo -e '${BOLD}Current docker environment variables${NORMAL}'
	@cat ./docker/.env
	@echo


build: test-docker-requirements ## Build docker images
	@cd docker && docker-compose build --build-arg DEVELOPER_UID=$(DEVELOPER_UID) && cd -

clean-volumes: down ## Removes docker volumes
	@docker volume rm $$(docker volume ls | grep $(VOLUME_PREFIX) | awk '{print $$2}')

clean-images: down ## Remove docker images
	docker rmi $$(docker images | grep ^$(IMAGE_PREFIX) | awk '{print $$1 ":" $$2}')

clean: ## Clean docker images and volumes
	@$(MAKE) -s clean-images
	@$(MAKE) -s clean-volumes

up: test-docker-requirements ## Docker: Create and start containers
	@cd docker && docker-compose up -d && cd -

down: test-docker-requirements ## Docker: Stop and remove containers, networks, images, and volumes
	@cd docker && docker-compose down && cd -

test-docker-requirements:
	@command -v docker >/dev/null 2>&1 || { echo "You need the 'docker' command..  Aborting." >&2; exit 1; }
	@command -v docker-compose >/dev/null 2>&1 || { echo "You need the 'docker-compose' command..  Aborting." >&2; exit 1; }

console: up ## Run application console (inside container)
	@docker exec -it ${CONTAINER_PREFIX}application bash