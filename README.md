# Sistema de Catálogo de Produtos de Limpeza

Sistema web completo desenvolvido em PHP puro com MySQL para catálogo de produtos de limpeza, incluindo carrinho de compras e finalização via WhatsApp.

## 🚀 Características

- **Frontend**: HTML5, CSS3, JavaScript vanilla, Bootstrap 5
- **Backend**: PHP 8.x (sem frameworks)
- **Banco de Dados**: MySQL 5.7+
- **Design**: Responsivo mobile-first
- **Segurança**: Prepared statements, sanitização de dados

## 📦 Funcionalidades

### Cliente
- ✅ Catálogo de produtos com imagens
- ✅ Carrinho de compras com sessão
- ✅ Controle de quantidade (+/-)
- ✅ Finalização via WhatsApp
- ✅ Design responsivo
- ✅ Notificações toast
- ✅ WhatsApp flutuante

### Administrativo
- ✅ Login seguro
- ✅ CRUD completo de produtos
- ✅ Upload de imagens
- ✅ Controle de estoque
- ✅ Dashboard com estatísticas

## 🛠️ Instalação

1. **Configurar Banco de Dados**
   ```bash
   # Importar estrutura do banco
   mysql -u root -p < sql/database.sql
   ```

2. **Configurar Conexão**
   ```php
   // Editar config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sistema_produtos_limpeza');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```

3. **Configurar WhatsApp**
   ```php
   // Editar config/database.php
   define('WHATSAPP_NUMBER', '5511999999999');
   ```

4. **Permissões de Pasta**
   ```bash
   chmod 755 assets/images/produtos/
   ```

## 🔐 Acesso Administrativo

**URL**: `/admin/login.php`
- **Usuário**: admin
- **Senha**: admin123

## 📁 Estrutura de Arquivos

```
projeto/
├── index.php (catálogo)
├── carrinho.php
├── finalizar.php
├── config/
│   └── database.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── produtos.php
│   └── logout.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── images/produtos/
└── sql/
    └── database.sql
```

## 🔧 Configurações

### Banco de Dados
- Host: localhost
- Banco: sistema_produtos_limpeza
- Charset: utf8

### Upload de Imagens
- Tamanho máximo: 5MB
- Formatos: JPG, PNG, GIF
- Pasta: assets/images/produtos/

### Sessão
- Timeout: 30 minutos
- Armazenamento: carrinho em $_SESSION

## 🚀 Recursos Implementados

### Segurança
- Prepared statements
- Sanitização de dados
- Validação de uploads
- Proteção contra XSS
- Verificação de autenticação

### Interface
- Design responsivo (Bootstrap 5)
- Animações CSS
- Loading spinners
- Notificações toast
- Hover effects

### JavaScript
- AJAX para carrinho
- Validação de formulários
- Controle de quantidade
- Smooth scroll
- Intersection Observer

## 📱 Responsividade

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px  
- **Desktop**: > 1024px

## 🎨 Personalização

### Cores (CSS Variables)
```css
:root {
    --primary-color: #007bff;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
}
```

### Configurações do Sistema
```php
// config/database.php
define('SITE_NAME', 'Produtos de Limpeza');
define('WHATSAPP_NUMBER', '5511999999999');
define('MAX_FILE_SIZE', 5242880);
```

## 🐛 Troubleshooting

### Erro de Conexão
```bash
# Verificar configurações do banco
# Testar conexão MySQL
mysql -u root -p
```

### Erro de Upload
```bash
# Verificar permissões
chmod 755 assets/images/produtos/
```

### Erro de Sessão
```bash
# Verificar configurações PHP
php -m | grep session
```

## 📝 Licença

Este projeto é de código aberto para fins educacionais.

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📞 Suporte

Para suporte, entre em contato via WhatsApp configurado no sistema.

---

**Desenvolvido com ❤️ em PHP puro + MySQL**