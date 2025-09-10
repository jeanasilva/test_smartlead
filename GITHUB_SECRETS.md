# 🔐 Configuração de Secrets - GitHub Actions

Para automatizar o deploy, você precisa configurar os seguintes secrets no GitHub:

## 📋 Como Configurar

1. **Vá para o seu repositório no GitHub**
2. **Settings → Secrets and variables → Actions**
3. **Clique em "New repository secret"**
4. **Adicione cada secret abaixo:**

## 🔑 Secrets Obrigatórios

### **SERVER_HOST**
```
IP do servidor VPS
```

### **SERVER_PASSWORD**
```
Senha root do servidor VPS
```

### **APP_KEY** 
```
Chave da aplicação Laravel (base64:...)
```

### **JWT_SECRET**
```
Chave JWT para autenticação
```

### **DB_PASSWORD**
```
Senha segura para o banco MySQL
```

### **MYSQL_ROOT_PASSWORD**
```
Senha segura para o root do MySQL
```

### **MAIL_PASSWORD**
```
Senha do email para notificações
```

## 🚀 Como Funciona

### **Deploy Automático**
- ✅ **Push para main** → Deploy automático
- ✅ **Manual trigger** → Deploy sob demanda
- ✅ **Secrets seguros** → Dados sensíveis protegidos
- ✅ **Ambiente isolado** → Criação automática do .env.prod

### **O que o Workflow faz:**

1. **📥 Checkout**: Baixa o código
2. **🔑 SSH Setup**: Configura conexão com servidor
3. **🚀 Deploy**: 
   - Clona/atualiza repositório no servidor
   - Cria `.env.prod` automaticamente com secrets
   - Executa `make deploy`
   - Configura Nginx se necessário
   - Configura SSL automaticamente
4. **🔍 Verify**: Testa se aplicação subiu
5. **📊 Summary**: Relatório do deploy

## 📝 Configuração da Chave SSH

### **Não é necessário configurar SSH manualmente!**

O GitHub Actions irá se conectar diretamente no servidor usando:**
- **IP do servidor** (via secret `SERVER_HOST`)
- **Senha do servidor** (via secret `SERVER_PASSWORD`)

### **Adicionar no GitHub:**
1. Vá para **Settings → Secrets and variables → Actions**
2. Adicione todos os secrets listados acima
3. **Não exponha dados sensíveis** no código público

## ⚡ Como Usar

### **Deploy Automático:**
```bash
git add .
git commit -m "feat: nova funcionalidade"
git push origin main
# → Deploy automático é executado!
```

### **Deploy Manual:**
1. Vá para **Actions** no GitHub
2. Selecione **"Deploy SmartLead to Production"**
3. Clique **"Run workflow"**
4. Aguarde a execução

## 🌐 URLs após Deploy

- **Aplicação**: https://smartlead.jeansilva.dev.br
- **API**: https://smartlead.jeansilva.dev.br/api  
- **Documentação**: https://smartlead.jeansilva.dev.br/api/documentation

## 🛡️ Segurança

- ✅ **Secrets criptografados** no GitHub
- ✅ **Conexão SSH segura** com o servidor
- ✅ **Ambiente de produção** isolado
- ✅ **SSL automático** via Let's Encrypt
- ✅ **Senhas fortes** geradas automaticamente

---

**Depois de configurar os secrets, faça um push e veja a mágica acontecer! ✨**
