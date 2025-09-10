# SmartLead API - Teste Técnico

## Descrição
API REST desenvolvida em Laravel com autenticação JWT e Docker para o teste técnico da SmartLead.

## Funcionalidades
- ✅ Autenticação JWT
- ✅ Sistema de registro e login
- ✅ Documentação Swagger/OpenAPI
- ✅ Docker com MySQL
- ✅ phpMyAdmin para gerenciamento
- ✅ Middleware de proteção de rotas
- ✅ Validação de requests
- ✅ Estrutura de API RESTful

## Tecnologias Utilizadas
- Laravel 8.x
- JWT (tymon/jwt-auth)
- Swagger (L5-Swagger)
- MySQL 8.0
- Docker & Docker Compose
- phpMyAdmin
- Redis (cache)

## Instalação com Docker (Recomendado)

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

- **API Laravel**: http://localhost:8000
- **Swagger Documentation**: http://localhost:8000/api/documentation  
- **phpMyAdmin**: http://localhost:8080
- **MySQL**: localhost:3306

### Credenciais MySQL
- **Database**: smartlead_db
- **Username**: smartlead
- **Password**: password
- **Root Password**: root

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

### 6. Inicie o servidor
```bash
php artisan serve
```

## API Endpoints

### Autenticação
- `POST /api/auth/register` - Registrar novo usuário
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
└── frontend/            # Aplicação Frontend (Em desenvolvimento)
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

## Status do Projeto

✅ **Backend Concluído:**
- Docker configurado
- Sistema de autenticação JWT
- Documentação Swagger
- MySQL configurado
- Middleware de proteção
- Validação de requests
- Estrutura RESTful

🔄 **Próximos Passos:**
- Desenvolvimento do Frontend
- Testes automatizados
- Deploy em produção

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
