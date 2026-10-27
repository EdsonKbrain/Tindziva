# Tindziva Comunicação & Ideias — Website e CMS

Website institucional, portfólio, blog e painel de gestão de conteúdos da Tindziva Comunicação & Ideias.

- **Stack:** Laravel 13 · PHP 8.4 · Blade · Tailwind CSS 4 · Alpine.js · Vite · MySQL/MariaDB
- **Autenticação:** Laravel Fortify (início de sessão, recuperação de palavra-passe, sem registo público)
- **Editor visual:** Quill 2, com HTML sanitizado no servidor (Symfony HtmlSanitizer)
- **Ícones:** Lucide (`mallardduck/blade-lucide-icons`, renderizados no servidor)

---

## 1. Requisitos

| Componente | Versão |
|---|---|
| PHP | 8.3 ou superior (testado em 8.4), com as extensões `pdo_mysql`, `gd` (com suporte WebP), `exif`, `intl`, `fileinfo`, `mbstring`, `zip` |
| Composer | 2.x |
| Node.js / npm | Node 20+ (apenas para compilar os assets) |
| MySQL / MariaDB | MySQL 8+ ou MariaDB 10.6+ |
| Servidor web | Nginx ou Apache com HTTPS |

## 2. Instalação

```bash
# 1. Dependências PHP
composer install --no-dev --optimize-autoloader     # em desenvolvimento: composer install

# 2. Dependências frontend
npm ci

# 3. Ficheiro de ambiente
cp .env.example .env
php artisan key:generate
```

### 3. Configurar o `.env`

Variáveis mais importantes:

```dotenv
APP_NAME="Tindziva Comunicação & Ideias"
APP_ENV=production
APP_DEBUG=false                 # nunca "true" em produção
APP_URL=https://tindziva.co.mz
APP_TIMEZONE=Africa/Maputo      # usado no agendamento de artigos

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tindziva
DB_USERNAME=tindziva
DB_PASSWORD=********

MEDIA_DISK=public               # ou "s3" para armazenamento externo
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true      # em produção com HTTPS
LOG_LEVEL=warning
```

O `.env` contém segredos: não o coloque no Git, defina permissões `640` e não o partilhe por e-mail ou chat.

### 4. Criar a base de dados MySQL

```sql
CREATE DATABASE tindziva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tindziva'@'localhost' IDENTIFIED BY 'uma-palavra-passe-forte';
GRANT ALL PRIVILEGES ON tindziva.* TO 'tindziva'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Executar as migrations e o conteúdo essencial

```bash
php artisan migrate --force
php artisan db:seed --force
```

O seeder principal cria apenas o conteúdo indispensável:

- as configurações base, com os contactos institucionais do portfólio 2024;
- as seis páginas do sistema (Início, Sobre nós, Serviços, Portfólio, Blog e Contactos), com os textos, a missão, a visão e os valores do portfólio 2024;
- os oito serviços do portfólio;
- os 39 clientes e parceiros do portfólio, com segmento, serviços prestados e logótipos (importados de `database/seeders/assets/clients` para a biblioteca multimédia).

### 6. Conteúdo de demonstração (opcional)

```bash
php artisan db:seed --class=DemoSeeder
```

Adiciona seis projectos e seis artigos de exemplo. Os projectos estão identificados como «demonstração» e devem ser substituídos pelos projectos reais antes do lançamento. Não execute este seeder em produção depois de existir conteúdo real.

### 7. Compilar os assets

```bash
npm run build        # produção
npm run dev          # desenvolvimento, com recarregamento automático
```

As fontes (Outfit, DM Sans e Instrument Serif) são descarregadas durante a compilação e servidas a partir do próprio servidor.

### 8. Armazenamento

```bash
php artisan storage:link
```

Os ficheiros da biblioteca multimédia ficam em `storage/app/public/media/AAAA/MM/`. Para usar armazenamento externo (S3, DigitalOcean Spaces, etc.), defina `MEDIA_DISK=s3` e preencha as variáveis `AWS_*` do `.env`.

### 9. Criar o primeiro superadministrador

```bash
php artisan tindziva:create-admin
```

O comando pede o nome, o e-mail e a palavra-passe de forma interactiva, sem os mostrar no ecrã. A palavra-passe tem de ter pelo menos 10 caracteres, com maiúsculas, minúsculas e números; em produção também é verificada contra fugas de dados conhecidas.

O projecto não cria contas com palavras-passe predefinidas. Os restantes utilizadores são criados no painel (`/admin/users`) e recebem um e-mail para definir a sua palavra-passe.

### 10. Configurar o SMTP

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.o-seu-fornecedor.com
MAIL_PORT=587
MAIL_SCHEME=null
MAIL_USERNAME=utilizador
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS="nao-responder@tindziva.co.mz"
MAIL_FROM_NAME="Tindziva Comunicação & Ideias"
```

O e-mail que recebe os novos pedidos e mensagens define-se no painel, em **Configurações → Contactos → E-mail para notificações**. Se o SMTP falhar, o formulário continua a guardar o pedido na base de dados e o erro fica registado em `storage/logs`.

### 11. Configurar o Scheduler

Os artigos agendados são publicados pelo comando `posts:publish-scheduled`, que o Scheduler do Laravel executa a cada minuto. Adicione esta linha ao crontab do utilizador do servidor web (`crontab -e -u www-data`):

```cron
* * * * * cd /var/www/tindziva && php artisan schedule:run >> /dev/null 2>&1
```

O Scheduler também remove diariamente os registos de actividade com mais de dois anos. Para confirmar que está activo:

```bash
php artisan schedule:list
```

### 12. HTTPS, permissões e backups

**HTTPS:** em produção (`APP_ENV=production`) a aplicação gera apenas URLs `https://` e envia cabeçalhos de segurança, incluindo HSTS quando o pedido chega por HTTPS. A configuração Nginx usada no VPS está em [deploy/nginx.conf](deploy/nginx.conf); o Certbot acrescenta o bloco HTTPS (ver «Deploy num VPS»).

**Permissões:**

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
chmod 640 .env
```

O servidor web deve apontar apenas para a pasta `public/`.

**Backups:** o script [deploy/backup.sh](deploy/backup.sh) exporta a base de dados (`mysqldump`) e comprime os ficheiros carregados. Lê as credenciais do `.env` e apaga cópias antigas automaticamente.

```cron
30 2 * * * /var/www/tindziva/deploy/backup.sh /var/www/tindziva /var/backups/tindziva 14
```

Guarde também uma cópia fora do servidor (por exemplo, com `rclone` para armazenamento na nuvem) e teste a reposição periodicamente:

```bash
gunzip < db-AAAAMMDD-HHMMSS.sql.gz | mysql -u tindziva -p tindziva
tar -xzf files-AAAAMMDD-HHMMSS.tar.gz -C storage/app
```

### Optimização em produção

Depois de cada actualização:

```bash
php artisan migrate --force
php artisan optimize          # cache de configuração, rotas, eventos e views
php artisan icons:cache       # cache dos ícones Lucide
```

---

## Deploy em alojamento DirectAdmin (servidor actual)

O alojamento da Tindziva é partilhado, com DirectAdmin, sem root e sem Node.js. Por isso, os assets são compilados no computador de desenvolvimento e enviados num pacote. No servidor, cada versão fica numa pasta própria e `public_html` aponta para a versão activa:

```
~/domains/tindziva.co.mz/
├── public_html -> app/current/public
└── app/
    ├── current -> releases/<versão activa>
    ├── releases/        últimas 3 versões
    └── shared/
        ├── .env         configuração de produção (permissões 600)
        └── storage/     uploads, logs, cache e sessões
```

O código está em [github.com/EdsonKbrain/Tindziva](https://github.com/EdsonKbrain/Tindziva). Em cada `git push` para `main`, o GitHub Actions ([production.yml](.github/workflows/production.yml)) corre os testes, compila os assets e publica o branch `production`. O servidor instala sempre a partir desse branch.

### Primeira instalação

1. **No DirectAdmin:**
   - Em **MySQL Management**, crie a base de dados e o utilizador.
   - Em **E-mail Accounts**, crie `nao-responder@tindziva.co.mz`.
   - Confirme que o domínio usa PHP 8.3 ou superior.
2. **No servidor (SSH):**

   ```bash
   cd ~/domains/tindziva.co.mz
   curl -fsSLO https://raw.githubusercontent.com/EdsonKbrain/Tindziva/main/deploy/directadmin/server.sh
   bash server.sh install
   ```

   O script descarrega o branch `production` e instala as dependências PHP. Depois pede os dados do MySQL e cria o `.env`, executa as migrations e o conteúdo essencial, liga a `public_html` ao Laravel (a original fica em `public_html.backup-…`), instala o cron e cria o superadministrador.
3. **No DirectAdmin:** em **SSL Certificates**, active o Let's Encrypt e a opção **Force SSL with https redirect**.
4. Preencha `MAIL_HOST`, `MAIL_USERNAME` e `MAIL_PASSWORD` em `app/shared/.env` e aplique com `bash ~/domains/tindziva.co.mz/app/current/deploy/directadmin/server.sh refresh`.

### Publicar actualizações

```bash
# no computador de desenvolvimento
git push                 # aguarde que o GitHub Actions termine (separador «Actions»)

# no servidor
bash ~/domains/tindziva.co.mz/app/current/deploy/directadmin/server.sh update
```

A nova versão é preparada à parte (dependências, migrations e caches) e só depois fica activa, sem tempo de inactividade. Em alternativa ao GitHub, pode gerar um pacote local com `deploy/directadmin/build-release.sh` e instalá-lo com `server.sh install <pacote.tar.gz>` ou `server.sh update <pacote.tar.gz>`.

Outros comandos do mesmo script:

| Comando | O que faz |
|---|---|
| `server.sh rollback` | Volta à versão anterior |
| `server.sh refresh` | Recarrega a configuração depois de editar o `.env` |
| `server.sh status` | Mostra a versão activa, o PHP e as tarefas agendadas |

## Deploy num VPS (Ubuntu 22.04 / 24.04)

Os ficheiros de deploy estão em [deploy/](deploy/):

| Ficheiro | Função |
|---|---|
| [provision.sh](deploy/provision.sh) | Preparação única do servidor: PHP 8.4, Nginx, MySQL, Composer, Node.js 22, Certbot, firewall, utilizador `tindziva`, base de dados, `.env`, PHP-FPM, Nginx e cron |
| [deploy.sh](deploy/deploy.sh) | Publicar uma nova versão: modo de manutenção, `git pull`, dependências, build, migrations e caches |
| [nginx.conf](deploy/nginx.conf) | Site Nginx (cache de assets, bloqueio de ficheiros ocultos e de PHP em `/storage`) |
| [php-fpm-pool.conf](deploy/php-fpm-pool.conf) | Pool PHP-FPM que executa a aplicação como o utilizador `tindziva` (uploads até 64 MB) |
| [crontab](deploy/crontab) | Scheduler a cada minuto e backup diário às 02:30 |
| [backup.sh](deploy/backup.sh) | Backup da base de dados e dos ficheiros carregados |
| [.env.production.example](.env.production.example) | Configuração de produção (`APP_DEBUG=false`, cookies seguros, logs diários) |

### Primeira instalação

1. Aponte o domínio (registos `A` de `tindziva.co.mz` e `www`) para o IP do servidor.
2. Coloque o código num repositório Git privado (GitHub, GitLab ou Bitbucket).
3. No servidor, como root:

   ```bash
   curl -fsSLO https://raw.githubusercontent.com/<organização>/<repositório>/main/deploy/provision.sh   # ou copie o ficheiro por scp
   bash provision.sh tindziva.co.mz git@github.com:<organização>/<repositório>.git ti@tindziva.co.mz
   ```

   O script mostra uma chave SSH pública: adicione-a ao repositório como **Deploy key** só de leitura e prima Enter. No fim, a palavra-passe gerada para o MySQL fica em `/root/tindziva-credenciais.txt`, só acessível ao root.

4. Configure o SMTP em `/var/www/tindziva/.env` e aplique:

   ```bash
   sudo -u tindziva bash -c "cd /var/www/tindziva && php artisan config:cache"
   ```

5. Crie o superadministrador:

   ```bash
   sudo -u tindziva bash -c "cd /var/www/tindziva && php artisan tindziva:create-admin"
   ```

### Publicar actualizações

Depois de enviar as alterações para o repositório (`git push`):

```bash
ssh utilizador@servidor
sudo -u tindziva bash -c "cd /var/www/tindziva && deploy/deploy.sh"
```

Durante o deploy, o website mostra a página de manutenção (cerca de 1 a 2 minutos). Se algum passo falhar, o site continua em manutenção para não servir uma versão incompleta. Corrija o problema e volte a executar o script, ou reponha o site com `php artisan up`.

Qualquer alteração ao `.env` só tem efeito depois de `php artisan config:cache`.

### Verificações após a instalação

- `https://tindziva.co.mz/up` responde `200`.
- `sudo -u tindziva crontab -l` mostra o scheduler e o backup.
- `sudo -u tindziva bash -c "cd /var/www/tindziva && php artisan schedule:list"` mostra a tarefa `posts:publish-scheduled`.
- Os logs ficam em `/var/www/tindziva/storage/logs/` (um ficheiro por dia, guardados 30 dias).

## 3. Perfis e permissões

As permissões são verificadas no servidor (gates, policies e middleware), não apenas escondidas na interface.

| Perfil | Pode |
|---|---|
| **Superadministrador** | Tudo, incluindo gerir utilizadores e outros superadministradores |
| **Administrador** | Gestão geral, excepto utilizadores. O superadministrador pode personalizar as permissões de cada administrador |
| **Editor** | Páginas, serviços, portfólio, clientes, todo o blog (incluindo publicar), categorias e multimédia |
| **Autor** | Criar e editar os próprios artigos (rascunhos, enviados para revisão) e carregar ficheiros |
| **Gestor comercial** | Pedidos de proposta, mensagens de contacto e newsletter |

Outras regras:

- Ninguém pode alterar o próprio perfil, desactivar a própria conta ou eliminar-se a si mesmo.
- Não é possível eliminar o último superadministrador activo.
- As contas desactivadas são desligadas de imediato.
- Alterar a palavra-passe termina as sessões noutros dispositivos.

## 4. Utilização do CMS (`/admin`)

| Área | Notas |
|---|---|
| **Páginas** | As páginas do sistema têm secções estruturadas: hero, apresentação, diferenciais, valores, equipa, CTA, etc. Use `*asteriscos*` num título para destacar uma palavra em itálico editorial. Cada secção tem um interruptor «Mostrar esta secção no website». Pode criar páginas personalizadas (por exemplo, *Política de privacidade*) e mostrá-las no menu. |
| **Textos e menu** | Todos os textos fixos do website: nomes e visibilidade dos itens do menu, botão do cabeçalho, chamada para acção, rótulos das páginas de serviço, projecto e artigo, campos e mensagens dos formulários, opções de orçamento e títulos do rodapé. Um campo vazio volta ao texto predefinido. Disponível para editores e administradores. |
| **Serviços** | Nome, slug, ícone, resumo, descrição, benefícios, imagem, galeria, categoria, ordem, publicação e SEO. |
| **Clientes** | Logótipo, segmento (PME, grandes empresas, Governo, empresas públicas, sociedade civil, cooperação internacional, projectos e marcas), serviços prestados, ordem e visibilidade. No website aparecem numa faixa na página inicial e agrupados por segmento no portfólio. |
| **Portfólio** | Capa, galeria ordenável (imagens e vídeos), vídeos do YouTube/Vimeo, categorias, serviços relacionados, cliente, créditos e registo das permissões de utilização. O cliente e os créditos só aparecem no website quando autorizados. |
| **Blog** | Editor visual, categorias, etiquetas, destaque, autor, estados (Rascunho, Publicado, Agendado), pré-visualização e SEO. O tempo de leitura é calculado automaticamente. |
| **Multimédia** | Upload por arrastar e largar. As imagens são redimensionadas (máx. 2400 px), recomprimidas e recebem miniaturas WebP. SVG e outros formatos executáveis são rejeitados por segurança. |
| **Propostas e mensagens** | Estados: Novo, Em análise, Em contacto, Proposta enviada, Concluído e Arquivado. Inclui notas internas, responsável, pesquisa, filtros por data e exportação CSV. |
| **Configurações** | Identidade, logótipos, favicon, contactos, mapa, redes sociais, WhatsApp, rodapé, SEO global, Google Analytics e newsletter. |
| **Actividade** | Regista inícios de sessão (incluindo tentativas falhadas) e criações, alterações e eliminações de conteúdos. |

Os serviços, projectos, artigos e páginas eliminados vão para a **Reciclagem** e podem ser restaurados.

## 5. SEO e desempenho

- Meta título, descrição e imagem Open Graph editáveis por conteúdo, com valores globais nas configurações.
- URLs amigáveis, canonical, `rel=prev/next` na paginação e `noindex` nas páginas de pesquisa.
- Dados estruturados: `Organization`, `BreadcrumbList`, `BlogPosting`, `Service` e `CreativeWork`.
- Sitemap dinâmico em `/sitemap.xml`. O `/robots.txt` bloqueia toda a indexação fora de produção.
- Imagens com `srcset`, `loading="lazy"` e dimensões explícitas; fontes servidas localmente.
- As consultas públicas ficam em cache e são invalidadas automaticamente quando o conteúdo é alterado no CMS.

## 6. Segurança

- CSRF, validação com Form Requests e escaping do Blade em todas as saídas.
- O HTML do editor é sanitizado ao guardar e novamente ao apresentar (`@richtext`).
- Rate limiting:
  - início de sessão: 5 tentativas por minuto;
  - formulários públicos: 5 por minuto e 50 por dia, por IP;
  - newsletter: 3 por minuto.
- Os formulários públicos têm um campo *honeypot* contra spam.
- Uploads validados por tipo MIME real e por tamanho (imagens até 10 MB, vídeos até 60 MB, PDF até 20 MB).
- Desserialização da cache restrita a classes autorizadas (`config/cache.php`).
- As exportações CSV são protegidas contra injecção de fórmulas.
- Cabeçalhos de segurança: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` e HSTS.
- Com `APP_DEBUG=false`, os erros mostram páginas próprias (403, 404, 419, 429, 500 e 503) sem expor detalhes técnicos.

## 7. Testes

```bash
php artisan test
```

Os testes usam SQLite em memória e cobrem:

- as páginas públicas e o SEO;
- os formulários, o anti-spam e o rate limiting;
- a autenticação;
- a matriz de permissões por perfil;
- o CRUD do CMS e o reflexo das alterações no website;
- o agendamento de artigos;
- a sanitização de HTML;
- a optimização de uploads.

## 8. Estrutura

```
app/
  Console/Commands/      posts:publish-scheduled, tindziva:create-admin
  Enums/                 Role, Permission, PostStatus, LeadStatus, MediaType, ClientType
  Http/Controllers/      Admin/ (CMS) e Site/ (website público)
  Http/Requests/         Validação (Admin/ e Site/)
  Models/Concerns/       HasSlug, HasSeo, HasGallery, LogsActivity, FlushesPublicCache
  Policies/              Post, Page, Media, User
  Services/              MediaService, HtmlSanitizer, SettingsRepository, PublicCache, ActivityLogger
  Support/               PageTemplates (esquema das secções), SectionSanitizer, SeoData, VideoEmbed, Text
resources/views/
  components/site/       Header, footer, cartões, formulários, SEO…
  components/admin/      Layout do painel, campos, selector multimédia, editor…
  site/  admin/  auth/  errors/  mail/
routes/web.php           Rotas públicas · routes/admin.php: rotas /admin · routes/console.php: Scheduler
deploy/                  backup.sh e exemplo de configuração Nginx
```

Para adicionar um campo editável a uma página do sistema, declare-o em `App\Support\PageTemplates::sections()` e use-o na view com `$page->section('secção.campo')`. O formulário do CMS e a sanitização são gerados automaticamente a partir desse esquema.
