# Makefile para SmartLead API

.PHONY: help build up down install migrate seed docs clean logs

help: ## Mostra esta ajuda
	@echo "SmartLead API - Comandos disponíveis:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Constroi as imagens Docker
	docker-compose build --no-cache

up: ## Inicia os containers
	docker-compose up -d

down: ## Para os containers
	docker-compose down

install: ## Instala dependências e configura a aplicação
	docker-compose up -d db
	@echo "Aguardando MySQL inicializar..."
	@sleep 15
	docker-compose up -d app
	@echo "Instalando dependências..."
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan jwt:secret --force
	@echo "Executando migrações..."
	docker-compose exec app php artisan migrate --force
	@echo "Gerando documentação..."
	docker-compose exec app php artisan l5-swagger:generate
	@echo "✅ Aplicação configurada com sucesso!"

migrate: ## Executa as migrações
	docker-compose exec app php artisan migrate

seed: ## Executa os seeders
	docker-compose exec app php artisan db:seed

docs: ## Gera documentação Swagger
	docker-compose exec app php artisan l5-swagger:generate

clean: ## Limpa cache da aplicação
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear

logs: ## Mostra os logs da aplicação
	docker-compose logs -f app

restart: down up ## Reinicia os containers

shell: ## Acessa o shell do container da aplicação
	docker-compose exec app bash

mysql: ## Acessa o MySQL
	docker-compose exec db mysql -u smartlead -p smartlead_db

status: ## Mostra status dos containers
	docker-compose ps

reset: ## Reseta completamente o ambiente
	docker-compose down -v
	docker-compose up -d
	$(MAKE) install
