# SmartLead - Sistema de Gerenciamento de Tarefas

## Descrição
Sistema completo de gerenciamento de tarefas e usuários desenvolvido com Laravel (API) + Vue.js (Frontend) para o teste técnico da SmartLead.

## 🚀 Demo
- **Frontend:** https://smartlead.jeansilva.dev.br
- **API Docs:** https://smartlead.jeansilva.dev.br/api/documentation
- **phpMyAdmin:** https://smartlead.jeansilva.dev.br/phpmyadmin

## Funcionalidades

### Backend (API)
- ✅ Autenticação JWT
- ✅ Sistema de registro e login
- ✅ Gerenciamento de usuários (CRUD)
- ✅ Gerenciamento de tarefas (CRUD)
- ✅ Gerenciamento de empresas
- ✅ Sistema de roles (Admin/User)
- ✅ Exportação de relatórios (PDF/CSV)
- ✅ Dashboard com estatísticas
- ✅ Documentação Swagger/OpenAPI
- ✅ Middleware de proteção de rotas
- ✅ Validação de requests
- ✅ Notificações por email (SMTP Zoho)

### Frontend (Vue.js)
- ✅ Interface moderna e responsiva
- ✅ Autenticação com JWT
- ✅ Dashboard interativo
- ✅ Gerenciamento de tarefas
- ✅ Sistema de filtros e busca
- ✅ Modais para CRUD operations
- ✅ Exportação de relatórios
- ✅ Gerenciamento de usuários (Admin only)
- ✅ Notificações e alerts
- ✅ Paginação
- ✅ Loading states e skeletons

## Tecnologias Utilizadas

### Backend
- Laravel 8.x
- JWT (tymon/jwt-auth)
- Swagger (L5-Swagger)
- DomPDF para exportação
- MySQL 8.0
- Redis (Cache)
- PHP 8.2

### Frontend
- Vue.js 2.x
- Vue Router
- Vuex (State Management)
- Axios (HTTP Client)
- Tailwind CSS
- SweetAlert2
- Font Awesome
- Nginx (Servidor web)

### Infraestrutura
- Docker & Docker Compose
- Nginx (Proxy Reverso)
- Redis (Cache)
- phpMyAdmin
- GitHub Actions (CI/CD)
- Cloudflare (CDN/SSL)

## Instalação Local

### Pré-requisitos
- Docker
- Docker Compose
- Make (opcional, mas recomendado)

### Método 1: Usando Makefile (Recomendado)
```bash
# Clone o projeto
git clone https://github.com/jeanasilva/test_smartlead.git
cd test_smartlead

# Configure tudo automaticamente
make install

# Ou veja todos os comandos disponíveis
make help
```

### Método 2: Comandos Docker tradicionais
```bash
# Clone o projeto
git clone https://github.com/jeanasilva/test_smartlead.git
cd test_smartlead

# Copie o .env
cp backend/.env.example backend/.env

# Inicie os containers
docker-compose up -d

# Configure a aplicação
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret --force
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan l5-swagger:generate
```

## Serviços Disponíveis

### Desenvolvimento
- **Frontend Vue.js**: http://localhost:3000
- **API Laravel**: http://localhost:8000
- **Swagger Documentation**: http://localhost:8000/api/documentation  
- **phpMyAdmin**: http://localhost:8080
- **MySQL**: localhost:3306

### Produção
- **Aplicação**: https://smartlead.jeansilva.dev.br
- **API**: https://smartlead.jeansilva.dev.br/api
- **Documentação**: https://smartlead.jeansilva.dev.br/api/documentation

### Credenciais MySQL
- **Database**: smartlead_db
- **Username**: smartlead
- **Password**: password
- **Root Password**: root

### Usuários de Teste
- **Admin**: admin@smartlead.com / password123
- **User**: user@smartlead.com / password123

## 🚀 Como Executar

### 🏠 Desenvolvimento Local

#### Método 1: Setup Automático (Recomendado)
```bash
# Clone o repositório
git clone <URL_DO_REPOSITORIO>
cd smartlead

# Execute a instalação completa
make install

# A aplicação estará disponível em:
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000  
# Documentação: http://localhost:8000/api/documentation
# phpMyAdmin: http://localhost:8080
```

#### Método 2: Setup Manual
```bash
# 1. Copie o arquivo de ambiente
cp backend/.env.example backend/.env

# 2. Suba os containers
docker-compose up -d --build

# 3. Instale as dependências do Laravel
docker-compose exec app composer install

# 4. Configure a aplicação
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret

# 5. Execute as migrações
docker-compose exec app php artisan migrate --seed

# 6. Gere a documentação
docker-compose exec app php artisan l5-swagger:generate
```

### 🌐 Deploy em Produção

#### Opção 1: Deploy Automático (GitHub Actions) ⭐ **RECOMENDADO**

**Deploy automático a cada push na main!**

1. **Configure os secrets do GitHub** (consulte [GITHUB_SECRETS.md](./GITHUB_SECRETS.md))
2. **Faça push** e veja o deploy automático acontecer! 

```bash
git add .
git commit -m "feat: nova funcionalidade"
git push origin main
# 🚀 Deploy automático executado!
```

**Status do deploy**: Acompanhe em **Actions** no GitHub

> 💡 **Não é necessário SSH** - O GitHub Actions se conecta diretamente no servidor usando senha.

#### Opção 2: Deploy Manual no Servidor

Para deploy manual, consulte o arquivo [DEPLOY.md](./DEPLOY.md) que contém instruções detalhadas.

```bash
# No servidor de produção
git clone https://github.com/jeanasilva/test_smartlead.git smartlead
cd smartlead
cp .env.prod.example .env.prod
# Configure .env.prod com suas credenciais
make deploy
```

**URL de Produção:** https://smartlead.jeansilva.dev.br

> ⚠️ **Nota de Portas**: Para evitar conflitos com outros serviços, o SmartLead rodará nas portas 8080 (HTTP) e 8443 (HTTPS). Configure seu proxy reverso principal para redirecionar o domínio para `localhost:8080`.

## 🛠️ Comandos Úteis

### Desenvolvimento
```bash
make install     # Instalação completa (primeira vez)
make dev         # Inicia ambiente de desenvolvimento  
make up          # Inicia os containers
make down        # Para os containers
make logs        # Mostra os logs
make shell       # Acessa shell do container
make mysql       # Acessa MySQL
make docs        # Regenera documentação Swagger
make clean       # Limpa cache
make restart     # Reinicia containers
make build       # Reconstrói imagens
```

### Produção
```bash
make prod        # Inicia ambiente de produção
make deploy      # Deploy completo (apenas no servidor)
```

## Instalação Manual (Sem Docker)

### 1. Clone o repositório
```bash
git clone https://github.com/jeanasilva/test_smartlead.git
cd test_smartlead/backend
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 4. Configure o MySQL
Altere as configurações no `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartlead_db
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Execute as migrações
```bash
php artisan migrate
```

### 6. Execute as migrações
```bash
php artisan migrate --seed
```

### 7. Gere a documentação
```bash
php artisan l5-swagger:generate
```

### 8. Inicie o servidor
```bash
php artisan serve
```

## 📡 API Endpoints

### Autenticação
- `POST /api/auth/register` - Registrar novo usuário
- `POST /api/auth/login` - Fazer login
- `POST /api/auth/logout` - Fazer logout  
- `POST /api/auth/refresh` - Renovar token
- `GET /api/auth/me` - Dados do usuário logado

### Tarefas (Tasks)
- `GET /api/tasks` - Listar tarefas do usuário
- `POST /api/tasks` - Criar nova tarefa
- `GET /api/tasks/{id}` - Obter tarefa específica
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Deletar tarefa
- `PATCH /api/tasks/{id}/complete` - Marcar como completa
- `PATCH /api/tasks/{id}/incomplete` - Marcar como incompleta

### Usuários (Admin)
- `GET /api/users/management` - Listar todos os usuários (admin)
- `PUT /api/users/{id}` - Atualizar usuário (admin)
- `DELETE /api/users/{id}` - Deletar usuário (admin)

### Empresas
- `GET /api/companies` - Listar empresas
- `POST /api/companies` - Criar nova empresa
- `PUT /api/companies/{id}` - Atualizar empresa
- `DELETE /api/companies/{id}` - Deletar empresa

## 🎨 Frontend Features

### Componentes Principais
- **Dashboard**: Visão geral das tarefas
- **TaskList**: Lista de tarefas com filtros e paginação
- **TaskModal**: Modal para criar/editar tarefas
- **Management**: Interface de gerenciamento de usuários (admin)
- **UserModal**: Modal para criar/editar usuários

### Recursos do Frontend
- ✅ Interface responsiva com Tailwind CSS
- ✅ Autenticação JWT integrada
- ✅ Gerenciamento de estado com Vuex
- ✅ Roteamento com Vue Router
- ✅ Notificações com SweetAlert2
- ✅ Filtros e paginação
- ✅ Validação de formulários
- ✅ Controle de acesso baseado em roles

## 🔒 Segurança

### Backend
- ✅ Autenticação JWT
- ✅ Middleware de autorização
- ✅ Validação de dados de entrada
- ✅ Rate limiting
- ✅ CORS configurado
- ✅ Sanitização de dados

### Produção
- ✅ SSL/HTTPS obrigatório
- ✅ Headers de segurança (HSTS, CSP)
- ✅ Nginx como proxy reverso
- ✅ Rate limiting no Nginx
- ✅ Backups automáticos do banco

## 🚀 Deploy em Produção

### Pré-requisitos do Servidor
- Ubuntu 20.04 LTS ou superior
- Docker & Docker Compose
- Domínio configurado (smartlead.jeansilva.dev.br)
- Portas 80 e 443 abertas
- Certificado SSL (Cloudflare/Let's Encrypt)

### Processo de Deploy
1. **Preparação**: Consulte [DEPLOY.md](./DEPLOY.md) para instruções detalhadas
2. **Configuração**: Configure `.env.prod` com credenciais seguras  
3. **Execução**: Execute `make deploy` no servidor
4. **Verificação**: Teste todas as funcionalidades

### URLs de Produção
- 🌐 **Aplicação**: https://smartlead.jeansilva.dev.br
- 🔗 **API**: https://smartlead.jeansilva.dev.br/api
- 📚 **Documentação**: https://smartlead.jeansilva.dev.br/api/documentation

## 📊 Monitoramento

### Logs
```bash
# Ver logs em tempo real
make logs

# Logs específicos do backend
docker-compose logs -f app

# Logs do Nginx (produção)  
docker-compose -f docker-compose.prod.yml logs -f nginx
```

### Backup do Banco
```bash
# Backup manual
docker-compose exec db mysqldump -u root -p smartlead_db > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker-compose exec -T db mysql -u root -p smartlead_db < backup.sql
```

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

- **Issues**: Reporte bugs e solicite features via GitHub Issues
- **Documentação**: Consulte a documentação da API em `/api/documentation`
- **Logs**: Use `make logs` para debug de problemas

---

**Desenvolvido com ❤️ para gerenciamento eficiente de tarefas empresariais**
- `POST /api/auth/login` - Fazer login
- `POST /api/auth/logout` - Fazer logout (requer token)
- `GET /api/auth/me` - Obter dados do usuário logado (requer token)
- `POST /api/auth/refresh` - Renovar token (requer token)

### Usuários
- `GET /api/users` - Listar usuários (requer token)
- `POST /api/users` - Criar usuário (requer token)
- `GET /api/users/{id}` - Obter usuário específico (requer token)
- `PUT /api/users/{id}` - Atualizar usuário (requer token)
- `DELETE /api/users/{id}` - Deletar usuário (requer token)

### Gerenciamento (Admin only)
- `GET /api/management/users` - Listar todos os usuários com filtros
- `POST /api/management/users` - Criar usuário (admin)
- `PUT /api/management/users/{id}` - Atualizar usuário (admin)
- `DELETE /api/management/users/{id}` - Deletar usuário (admin)

### Tarefas
- `GET /api/tasks` - Listar tarefas
- `POST /api/tasks` - Criar tarefa
- `GET /api/tasks/{id}` - Obter tarefa específica
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Deletar tarefa

### Empresas
- `GET /api/companies` - Listar empresas
- `POST /api/companies` - Criar empresa (admin only)
- `PUT /api/companies/{id}` - Atualizar empresa (admin only)
- `DELETE /api/companies/{id}` - Deletar empresa (admin only)

### Dashboard
- `GET /api/dashboard/stats` - Estatísticas gerais
- `GET /api/dashboard/recent-tasks` - Tarefas recentes
- `GET /api/dashboard/my-tasks` - Minhas tarefas

### Exportação
- `GET /api/export/tasks/csv` - Exportar tarefas em CSV
- `GET /api/export/tasks/pdf` - Exportar tarefas em PDF
- `GET /api/export/report/summary` - Relatório resumido

### Perfil
- `GET /api/profile` - Dados do perfil
- `PUT /api/profile` - Atualizar perfil
- `PUT /api/profile/password` - Alterar senha
- `GET /api/profile/stats` - Estatísticas pessoais

## Exemplo de Uso

### 1. Registrar um usuário
```bash
curl -X POST http://localhost:8000/api/auth/register \
-H "Content-Type: application/json" \
-d '{
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "password": "password123",
  "password_confirmation": "password123"
}'
```

### 2. Fazer login
```bash
curl -X POST http://localhost:8000/api/auth/login \
-H "Content-Type: application/json" \
-d '{
  "email": "joao@exemplo.com",
  "password": "password123"
}'
```

### 3. Usar token para acessar rotas protegidas
```bash
curl -X GET http://localhost:8000/api/auth/me \
-H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## Estrutura do Projeto

```
test_smartlead/
├── docker-compose.yml      # Configuração dos containers
├── Dockerfile             # Imagem PHP/Laravel
├── Makefile              # Comandos automatizados
├── README.md             # Documentação
├── .gitignore           # Arquivos ignorados pelo Git
│
├── backend/             # Aplicação Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Middleware/
│   │   │   │   └── JWTMiddleware.php
│   │   │   └── Requests/
│   │   │       ├── LoginRequest.php
│   │   │       └── RegisterRequest.php
│   │   └── Models/
│   │       └── User.php
│   ├── config/
│   │   └── l5-swagger.php
│   ├── database/
│   │   └── migrations/
│   └── routes/
│       ├── api.php
│       └── web.php
│
└── frontend/            # Aplicação Vue.js
    ├── public/
    ├── src/
    │   ├── assets/
    │   │   └── css/
    │   │       └── main.css
    │   ├── components/
    │   │   ├── layout/
    │   │   │   ├── AppLayout.vue
    │   │   │   ├── AppHeader.vue
    │   │   │   └── AppFooter.vue
    │   │   └── modals/
    │   │       └── UserModal.vue
    │   ├── router/
    │   │   └── index.js
    │   ├── services/
    │   │   ├── api.js
    │   │   └── auth.js
    │   ├── store/
    │   │   ├── index.js
    │   │   └── modules/
    │   │       └── auth.js
    │   ├── views/
    │   │   ├── Dashboard.vue
    │   │   ├── Tasks.vue
    │   │   ├── Management.vue
    │   │   ├── Reports.vue
    │   │   ├── Login.vue
    │   │   └── Register.vue
    │   ├── App.vue
    │   └── main.js
    ├── package.json
    └── vue.config.js
```

## Comandos Docker Úteis

### Usando Makefile
```bash
make help          # Ver todos os comandos
make up            # Subir containers
make down          # Parar containers
make logs          # Ver logs
make clean         # Limpar cache
make shell         # Acessar container
make mysql         # Acessar MySQL
make docs          # Gerar documentação
make restart       # Reiniciar containers
```

### Comandos Docker tradicionais
```bash
# Subir containers
docker-compose up -d

# Ver logs
docker-compose logs -f app

# Executar comandos Artisan
docker-compose exec app php artisan [comando]

# Acessar container
docker-compose exec app bash

# Parar containers
docker-compose down
```

## Deploy em Produção

### Estrutura do Servidor (Ubuntu 20.04 LTS)
- **Domínio**: smartlead.jeansilva.dev.br
- **SSL**: Cloudflare (Automatic HTTPS)
- **Proxy**: Nginx
- **Containers**: Docker Compose

### Deploy Automatizado
```bash
# No servidor de produção
git clone https://github.com/jeanasilva/test_smartlead.git
cd test_smartlead

# Executar script de deploy
chmod +x deploy.sh
./deploy.sh
```

### Comandos de Deploy Manual
```bash
# Parar containers antigos
docker-compose -f docker-compose.prod.yml down

# Fazer pull das últimas alterações
git pull origin main

# Rebuild e start
docker-compose -f docker-compose.prod.yml up -d --build

# Limpar cache
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
```

## Status do Projeto

✅ **Backend Concluído:**
- Sistema de autenticação JWT
- CRUD completo de usuários, tarefas e empresas
- Sistema de roles e permissões
- Dashboard com estatísticas
- Exportação de relatórios (PDF/CSV)
- Documentação Swagger completa
- Middleware de proteção
- Validação de requests
- Notificações por email

✅ **Frontend Concluído:**
- Interface moderna e responsiva
- Autenticação completa
- Dashboard interativo
- Gerenciamento completo de tarefas
- Sistema de filtros avançado
- Gerenciamento de usuários (Admin)
- Exportação de relatórios
- Notificações e feedback visual
- Loading states e UX otimizada

✅ **DevOps/Deploy:**
- Docker configurado para desenvolvimento e produção
- Nginx como proxy reverso
- SSL via Cloudflare
- Deploy automatizado
- Configuração de produção otimizada

## Troubleshooting

### Problema com permissões
```bash
docker-compose exec app chown -R laravel:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
```

### Reconstruir containers
```bash
docker-compose down
docker-compose up -d --build
```

### Limpar cache Laravel
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

## Contato

Desenvolvido por Jean Silva para o teste técnico da SmartLead.

