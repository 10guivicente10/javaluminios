# ALUMÍNIOS PREMIUM - WEBSITE PROFISSIONAL

Site profissional completo para empresa de alumínios com sistema de gestão de contactos.

## 📁 ESTRUTURA DO PROJETO

```
aluminios-site/
├── index.html              # Página principal
├── servicos.html           # Página de serviços
├── portfolio.html          # Portfolio (criar)
├── sobre.html              # Sobre a empresa (criar)
├── contacto.html           # Página de contacto
├── privacidade.html        # Política de privacidade (criar)
├── termos.html             # Termos e condições (criar)
│
├── css/
│   └── style.css           # Estilos principais
│
├── js/
│   ├── main.js             # JavaScript principal
│   └── form-handler.js     # Gestão de formulários
│
├── api/
│   ├── submit-contact.php  # API para processar formulários
│   ├── database.sql        # Schema da base de dados
│   └── config.php          # Configurações (criar)
│
└── images/                 # Pasta para imagens
    └── (adicionar imagens reais)
```

## 🚀 INSTALAÇÃO E CONFIGURAÇÃO

### PASSO 1: Preparar o Ambiente

#### Opção A: Servidor Local (Desenvolvimento)
1. Instala XAMPP ou MAMP:
   - Windows: https://www.apachefriends.org/
   - Mac: https://www.mamp.info/

2. Copia a pasta `aluminios-site` para:
   - XAMPP: `C:\xampp\htdocs\`
   - MAMP: `/Applications/MAMP/htdocs/`

#### Opção B: Hosting Online (Produção)
1. Escolhe um serviço de hosting:
   - PTServidor (português): https://www.ptservidor.pt/
   - Hostinger: https://www.hostinger.pt/
   - SiteGround: https://www.siteground.com/

2. Contrata:
   - Hosting PHP com MySQL
   - Domínio (ex: aluminios-pai.pt)

### PASSO 2: Configurar Base de Dados

1. Acede ao phpMyAdmin:
   - Local: http://localhost/phpmyadmin
   - Hosting: através do painel de controlo

2. Cria a base de dados:
   - Clica em "New" / "Nova"
   - Nome: `aluminios_db`
   - Collation: `utf8mb4_unicode_ci`

3. Importa o schema:
   - Seleciona a base de dados
   - Tab "Import" / "Importar"
   - Escolhe o ficheiro `api/database.sql`
   - Clica "Go" / "Executar"

4. Cria utilizador:
   ```sql
   CREATE USER 'aluminios_user'@'localhost' IDENTIFIED BY 'PASSWORD_FORTE';
   GRANT ALL ON aluminios_db.* TO 'aluminios_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### PASSO 3: Configurar o Backend

1. Abre `api/submit-contact.php`

2. Atualiza as credenciais da base de dados:
   ```php
   $dbConfig = [
       'host' => 'localhost',
       'dbname' => 'aluminios_db',
       'username' => 'aluminios_user',
       'password' => 'A_TUA_PASSWORD_AQUI',
   ];
   ```

3. Atualiza o email da empresa:
   ```php
   $to = 'o-teu-email@aluminios.pt';
   ```

### PASSO 4: Personalizar o Site

1. **Informações de Contacto**
   - Atualiza em TODAS as páginas:
     - Telefone: `+351 XXX XXX XXX`
     - Email: `geral@aluminios.pt`
     - Morada completa

2. **Imagens**
   - Adiciona imagens reais na pasta `images/`:
     - janelas.jpg
     - portas.jpg
     - fachadas.jpg
     - estores.jpg
     - varandas.jpg
     - medida.jpg
   - Tamanho recomendado: 800x600px

3. **Textos**
   - Personaliza anos de experiência
   - Número de projetos
   - Estatísticas da empresa

4. **Google Maps** (opcional)
   - Substitui `.map-placeholder` em `contacto.html`
   - Usa Google Maps Embed API

### PASSO 5: Fazer Upload (Se for para hosting)

1. Liga-te via FTP:
   - FileZilla: https://filezilla-project.org/
   - Credenciais fornecidas pelo hosting

2. Faz upload de todos os ficheiros para a pasta `public_html/`

3. Testa o site: `http://teu-dominio.pt`

## 📱 TESTAR NO TELEMÓVEL

### Localmente:
1. Descobre o teu IP:
   - Windows: `ipconfig` no CMD
   - Mac: `ifconfig` no Terminal

2. No telemóvel (mesma rede WiFi):
   - Acede: `http://SEU_IP/aluminios-site`
   - Exemplo: `http://192.168.1.100/aluminios-site`

### Online:
- Acede ao domínio: `http://teu-dominio.pt`

## 🔒 SEGURANÇA

### IMPORTANTE - Fazer ANTES de por online:

1. **Mudar passwords:**
   - Base de dados
   - Utilizador admin do painel

2. **Configurar HTTPS:**
   - A maioria dos hostings oferece SSL grátis (Let's Encrypt)
   - Ativa no painel de controlo

3. **Proteção de ficheiros:**
   - Adiciona `.htaccess`:
   ```apache
   # Proteger ficheiros sensíveis
   <FilesMatch "\.(sql|md|log)$">
       Order allow,deny
       Deny from all
   </FilesMatch>
   ```

4. **Backup regular:**
   - Base de dados (exporta semanalmente)
   - Ficheiros do site

## 📊 PAINEL DE ADMINISTRAÇÃO (Opcional)

Para criar um painel para gerir os contactos:

1. Cria `admin/` folder
2. Sistema de login
3. Lista de contactos
4. Ver/editar pedidos
5. Enviar orçamentos

(Posso criar isto se precisares!)

## 🎨 PERSONALIZAÇÃO ADICIONAL

### Cores:
Edita em `css/style.css`:
```css
:root {
    --primary-color: #d4af37;  /* Dourado */
    --secondary-color: #2a2d34; /* Cinza escuro */
    --accent-color: #4a90e2;   /* Azul */
}
```

### Fontes:
Muda em `<head>` de cada HTML:
```html
<link href="https://fonts.googleapis.com/css2?family=TUA_FONTE&display=swap" rel="stylesheet">
```

## 📧 CONFIGURAR EMAIL

Para emails funcionarem corretamente:

1. **Opção A: Email do Hosting**
   - Cria conta no painel: `noreply@teu-dominio.pt`
   - Usa SMTP em vez de `mail()`

2. **Opção B: Gmail/Outlook**
   - Usa biblioteca PHPMailer
   - Configura SMTP externo

## 📈 ANALYTICS E MARKETING

### Google Analytics:
```html
<!-- Adiciona antes de </head> -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Facebook Pixel:
```html
<!-- Adiciona antes de </head> -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'YOUR_PIXEL_ID');
  fbq('track', 'PageView');
</script>
```

## 🆘 PROBLEMAS COMUNS

### Formulário não envia:
1. Verifica se PHP está ativo
2. Confirma credenciais da BD
3. Vê erros em `api/submit-contact.php`
4. Testa email: `php -r "mail('test@test.com','Test','Test');"`

### Imagens não aparecem:
1. Verifica caminhos (case-sensitive)
2. Confirma permissões (chmod 755)
3. Usa caminhos relativos

### Site não carrega CSS/JS:
1. Verifica caminhos dos ficheiros
2. Limpa cache do browser (Ctrl+Shift+R)
3. Confirma ficheiros estão carregados

## 📞 SUPORTE

Para dúvidas ou problemas:
1. Verifica este README
2. Testa localmente primeiro
3. Vê logs de erro do servidor
4. Contacta o suporte do hosting

## ✅ CHECKLIST DE LANÇAMENTO

Antes de por o site online:

- [ ] Base de dados criada e configurada
- [ ] Todas as passwords alteradas
- [ ] Informações de contacto atualizadas
- [ ] Imagens reais adicionadas
- [ ] Textos personalizados
- [ ] Formulário testado e funcional
- [ ] Email de confirmação funciona
- [ ] HTTPS ativado
- [ ] Testado em desktop e mobile
- [ ] Google Analytics configurado
- [ ] Backup criado

## 🎯 PRÓXIMOS PASSOS

Depois do site estar online:
1. Regista no Google Search Console
2. Cria conta Google My Business
3. Adiciona no Facebook/Instagram
4. Pede reviews aos clientes
5. Cria campanhas Google Ads
6. Otimiza SEO

---

**Boa sorte com o site! 🚀**