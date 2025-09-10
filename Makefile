# Makefile para o projeto SmartLead

.PHONY: help install up down logs clean shell mysql docs restart build dev prod deploy

# Cores para output
BLUE=\033[0;34m
GREEN=\033[0;32m
YELLOW=\033[1;33m
RED=\033[0;31m
NC=\033[0m # No Color

help: ## Mostra esta ajuda
	@echo "$(BLUE)SmartLead - Comandos disponíveis:$(NC)"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ""

install: ## Instala o projeto completo (desenvolvimento)
	@echo "$(YELLOW)📦 Instalando SmartLead (Desenvolvimento)...$(NC)"
	@if [ ! -f backend/.env ]; then cp backend/.env.example backend/.env; fi
	@docker-compose up -d --build
	@echo "$(YELLOW)⏳ Aguardando containers ficarem prontos...$(NC)"
	@sleep 20
	@docker-compose exec app composer install
	@docker-compose exec app php artisan key:generate --force
	@docker-compose exec app php artisan jwt:secret --force
	@docker-compose exec app php artisan migrate --force
	@docker-compose exec app php artisan db:seed --force
	@docker-compose exec app php artisan l5-swagger:generate
	@echo "$(GREEN)✅ Instalação concluída!$(NC)"
	@echo ""
	@echo "$(BLUE)🌐 Serviços disponíveis:$(NC)"
	@echo "  Frontend:     http://localhost:3000"
	@echo "  API:          http://localhost:8000"
	@echo "  Documentação: http://localhost:8000/api/documentation"
	@echo "  phpMyAdmin:   http://localhost:8080"
	@echo ""

dev: ## Inicia ambiente de desenvolvimento
	@echo "$(YELLOW)🚀 Iniciando ambiente de desenvolvimento...$(NC)"
	@docker-compose up -d
	@echo "$(GREEN)✅ Ambiente iniciado!$(NC)"
	@echo "  Frontend: http://localhost:3000"
	@echo "  Backend:  http://localhost:8000"

prod: ## Inicia ambiente de produção
	@echo "$(YELLOW)🚀 Iniciando ambiente de produção...$(NC)"
	@if [ ! -f .env.prod ]; then echo "$(RED)❌ Arquivo .env.prod não encontrado!$(NC)"; exit 1; fi
	@docker-compose -f docker-compose.prod.yml up -d --build
	@echo "$(GREEN)✅ Ambiente de produção iniciado!$(NC)"

deploy: ## Execute o deploy completo (apenas no servidor)
	@echo "$(YELLOW)🚀 Executando deploy...$(NC)"
	@chmod +x deploy.sh
	@./deploy.sh

up: ## Inicia os containers (desenvolvimento)
	@echo "$(YELLOW)🚀 Iniciando containers...$(NC)"
	@docker-compose up -d

down: ## Para os containers
	@echo "$(YELLOW)🛑 Parando containers...$(NC)"
	@docker-compose down
	@docker-compose -f docker-compose.prod.yml down || true

logs: ## Mostra os logs dos containers
	@docker-compose logs -f

clean: ## Limpa cache e containers
	@echo "$(YELLOW)🧹 Limpando cache...$(NC)"
	@docker-compose exec app php artisan cache:clear || true
	@docker-compose exec app php artisan config:clear || true
	@docker system prune -f

shell: ## Acessa o shell do container da aplicação
	@docker-compose exec app bash

mysql: ## Acessa o MySQL
	@docker-compose exec db mysql -u smartlead -ppassword smartlead_db

docs: ## Regenera a documentação Swagger
	@echo "$(YELLOW)📚 Gerando documentação...$(NC)"
	@docker-compose exec app php artisan l5-swagger:generate
	@echo "$(GREEN)✅ Documentação gerada!$(NC)"
	@echo "  Acesse: http://localhost:8000/api/documentation"

restart: ## Reinicia os containers
	@echo "$(YELLOW)🔄 Reiniciando containers...$(NC)"
	@docker-compose restart

build: ## Reconstrói as imagens Docker
	@echo "$(YELLOW)🏗️ Reconstruindo imagens...$(NC)"
	@docker-compose build --no-cache

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
