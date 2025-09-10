#!/bin/bash

# Script de Deploy para SmartLead
# Desenvolvido para Ubuntu 20.04 LTS

set -e

echo "🚀 Iniciando deploy do SmartLead..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

# Verificar se está executando como root
if [ "$EUID" -ne 0 ]; then
    error "Este script deve ser executado como root"
fi

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    log "Instalando Docker..."
    apt-get update
    apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
    apt-get update
    apt-get install -y docker-ce docker-ce-cli containerd.io
fi

# Verificar se Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    log "Instalando Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# Verificar se o projeto já existe
PROJECT_DIR="/opt/smartlead"

if [ ! -d "$PROJECT_DIR" ]; then
    log "Clonando repositório..."
    mkdir -p /opt
    cd /opt
    git clone https://github.com/jeanasilva/test_smartlead.git smartlead
else
    log "Atualizando repositório..."
    cd $PROJECT_DIR
    git pull origin main
fi

cd $PROJECT_DIR

# Criar arquivo .env de produção se não existir
if [ ! -f ".env.prod" ]; then
    log "Criando arquivo .env de produção..."
    cat > .env.prod << EOF
# Database
DB_PASSWORD=$(openssl rand -hex 16)
MYSQL_ROOT_PASSWORD=$(openssl rand -hex 16)

# Application
APP_KEY=$(openssl rand -hex 32)
JWT_SECRET=$(openssl rand -hex 32)

# URLs
APP_URL=https://smartlead.jeansilva.dev.br
VUE_APP_API_BASE_URL=https://smartlead.jeansilva.dev.br/api
EOF
    warning "Arquivo .env.prod criado. Verifique as configurações antes de continuar."
fi

# Carregar variáveis de ambiente
source .env.prod

# Parar containers existentes
log "Parando containers existentes..."
docker-compose -f docker-compose.prod.yml down || true

# Fazer backup do banco de dados (se existir)
if docker ps -a | grep -q smartlead_mysql; then
    log "Fazendo backup do banco de dados..."
    docker exec smartlead_mysql mysqldump -u root -p$MYSQL_ROOT_PASSWORD smartlead_db > backup_$(date +%Y%m%d_%H%M%S).sql || warning "Falha no backup"
fi

# Build e start dos containers
log "Construindo e iniciando containers..."
docker-compose -f docker-compose.prod.yml up -d --build

# Aguardar containers ficarem prontos
log "Aguardando containers ficarem prontos..."
sleep 30

# Executar migrações e configurações do Laravel
log "Configurando Laravel..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan jwt:secret --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan db:seed --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan l5-swagger:generate

# Verificar se os serviços estão funcionando
log "Verificando serviços..."
sleep 10

if curl -f http://localhost/health > /dev/null 2>&1; then
    log "✅ Nginx está funcionando"
else
    error "❌ Nginx não está respondendo"
fi

if curl -f http://localhost/api/health > /dev/null 2>&1; then
    log "✅ API está funcionando"
else
    error "❌ API não está respondendo"
fi

# Limpeza
log "Limpando containers e imagens antigas..."
docker system prune -f

# Configurar logrotate
if [ ! -f "/etc/logrotate.d/smartlead" ]; then
    log "Configurando logrotate..."
    cat > /etc/logrotate.d/smartlead << EOF
/var/log/nginx/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 nginx nginx
    sharedscripts
    postrotate
        docker exec smartlead_nginx nginx -s reload > /dev/null 2>&1 || true
    endscript
}
EOF
fi

# Configurar crontab para backups automáticos
if ! crontab -l | grep -q "smartlead_backup"; then
    log "Configurando backup automático..."
    (crontab -l 2>/dev/null; echo "0 2 * * * cd $PROJECT_DIR && docker exec smartlead_mysql mysqldump -u root -p$MYSQL_ROOT_PASSWORD smartlead_db > backup_\$(date +\%Y\%m\%d).sql") | crontab -
fi

log "🎉 Deploy concluído com sucesso!"

echo ""
echo -e "${YELLOW}🌐 CONFIGURAÇÃO DO PROXY REVERSO PRINCIPAL${NC}"
echo "═══════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}Para conectar o domínio smartlead.jeansilva.dev.br:${NC}"
echo ""
echo "1. Copie a configuração nginx:"
echo "   sudo cp nginx-server-config.conf /etc/nginx/sites-available/smartlead"
echo ""
echo "2. Habilite o site:"
echo "   sudo ln -s /etc/nginx/sites-available/smartlead /etc/nginx/sites-enabled/"
echo ""
echo "3. Teste a configuração:"
echo "   sudo nginx -t"
echo ""
echo "4. Recarregue o nginx:"
echo "   sudo systemctl reload nginx"
echo ""
echo "5. Configure SSL (Let's Encrypt):"
echo "   sudo certbot --nginx -d smartlead.jeansilva.dev.br"
echo ""
echo -e "${GREEN}URLs após configuração:${NC}"
echo "� Interno: http://localhost:8080"
echo "🌐 Externo: https://smartlead.jeansilva.dev.br"
echo "📚 API Docs: https://smartlead.jeansilva.dev.br/api/documentation"

echo ""
echo -e "${BLUE}Comandos úteis:${NC}"
echo "  Ver logs:           docker-compose -f docker-compose.prod.yml logs -f"
echo "  Restart:            docker-compose -f docker-compose.prod.yml restart"
echo "  Status:             docker-compose -f docker-compose.prod.yml ps"
echo "  Shell do Laravel:   docker-compose -f docker-compose.prod.yml exec app bash"
echo ""
