# GUIA RÁPIDO - ALUMÍNIOS PREMIUM

## 🚀 COMEÇAR RAPIDAMENTE

### 1. TESTAR LOCALMENTE (5 minutos)

```bash
# Se tens Python instalado:
cd aluminios-site
python -m http.server 8000

# Abre no browser:
http://localhost:8000
```

**NOTA:** O formulário não funcionará sem PHP+MySQL. Só para ver o design.

---

### 2. COM XAMPP/MAMP (15 minutos)

1. **Instalar XAMPP:**
   - Download: https://www.apachefriends.org/
   - Instala e abre o painel

2. **Copiar ficheiros:**
   - Copia pasta `aluminios-site` para `C:\xampp\htdocs\`

3. **Criar Base de Dados:**
   - Abre: http://localhost/phpmyadmin
   - Clica "New" → Nome: `aluminios_db`
   - Tab "Import" → Escolhe `api/database.sql`

4. **Configurar:**
   - Abre `api/submit-contact.php`
   - Linha 81-85: atualiza password da BD
   - Linha 137: coloca o teu email

5. **Testar:**
   - Abre: http://localhost/aluminios-site
   - Vai a Contacto e testa o formulário

---

### 3. COLOCAR ONLINE (30 minutos)

#### A. Comprar Hosting

**Opção 1: PTServidor (Português)**
- Site: https://www.ptservidor.pt/
- Escolhe: "Hosting Linux + Domínio"
- Preço: ~30€/ano

**Opção 2: Hostinger**
- Site: https://www.hostinger.pt/
- Escolhe: "Premium Hosting"
- Preço: ~3€/mês

#### B. Upload de Ficheiros

1. **Via cPanel:**
   - Login no painel do hosting
   - File Manager → public_html
   - Upload todos os ficheiros

2. **Via FTP (FileZilla):**
   - Download: https://filezilla-project.org/
   - Liga com credenciais do hosting
   - Arrasta ficheiros para `/public_html/`

#### C. Configurar Base de Dados

1. No painel do hosting:
   - MySQL Databases → Create Database
   - Nome: `aluminios_db`

2. Importar schema:
   - phpMyAdmin → Import
   - Escolhe `api/database.sql`

3. Atualizar `submit-contact.php` com credenciais

#### D. Ativar HTTPS

- No painel do hosting
- SSL/TLS → Let's Encrypt (grátis)
- Ativa para o domínio

---

## ✅ CHECKLIST MÍNIMO

Antes de lançar:

- [ ] Base de dados criada
- [ ] Ficheiro `submit-contact.php` configurado
- [ ] Email da empresa atualizado
- [ ] Telefone atualizado em todas as páginas
- [ ] Testado formulário de contacto
- [ ] HTTPS ativado

---

## 📱 VER NO TELEMÓVEL

### Local (mesma WiFi):
1. Descobre teu IP: `ipconfig` (Windows) ou `ifconfig` (Mac)
2. No telemóvel: `http://SEU_IP/aluminios-site`

### Online:
- Acede ao domínio: `www.teu-dominio.pt`

---

## 🆘 PROBLEMAS?

### Formulário não envia:
```
1. Verifica se o Apache está ligado (XAMPP)
2. Confirma credenciais da BD em submit-contact.php
3. Vê erros: C:\xampp\apache\logs\error.log
```

### Página em branco:
```
1. Verifica erros PHP (ativa display_errors)
2. Confirma todos os ficheiros foram copiados
3. Limpa cache do browser (Ctrl+Shift+R)
```

### CSS não carrega:
```
1. Verifica caminho: css/style.css (não CSS/style.css)
2. Confirma ficheiro existe
3. Limpa cache
```

---

## 📞 PERSONALIZAÇÃO RÁPIDA

### 1. Cores (css/style.css, linha 8-15):
```css
--primary-color: #d4af37;  /* Muda aqui */
```

### 2. Contactos (TODAS as páginas):
```html
📞 +351 XXX XXX XXX  → O TEU NÚMERO
📧 geral@aluminios.pt → O TEU EMAIL
```

### 3. Imagens:
- Adiciona em `/images/`
- Nomes: janelas.jpg, portas.jpg, etc.
- Tamanho: 800x600px

---

## 🎯 APÓS LANÇAMENTO

1. **Google My Business:**
   - https://www.google.com/business/
   - Cria perfil da empresa

2. **Google Search Console:**
   - https://search.google.com/search-console
   - Adiciona o site

3. **Facebook Page:**
   - Cria página da empresa
   - Liga ao site

---

## 💡 DICAS PROFISSIONAIS

### SEO Básico:
- Títulos únicos em cada página
- Descrições relevantes (meta tags)
- Imagens com alt text
- URLs amigáveis

### Performance:
- Comprime imagens (tinypng.com)
- Ativa cache
- Usa CDN (opcional)

### Segurança:
- HTTPS sempre ativo
- Passwords fortes
- Backups semanais
- Atualiza PHP regularmente

---

**PRONTO! Bom trabalho! 🎉**

Tens dúvidas? Relê o README.md completo.