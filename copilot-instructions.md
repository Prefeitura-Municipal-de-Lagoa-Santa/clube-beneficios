# Portal de Emendas e Parcerias — Copilot Instructions

> Sistema de transparência para gestão de emendas parlamentares e parcerias/repasses da Prefeitura Municipal de Lagoa Santa (MG).

## Quick Reference

```bash
# Instalar dependências
composer install && npm install

# Desenvolvimento (servidor + queue + vite)
composer dev

# Testes
composer test            # PHPUnit/Pest
npm run lint             # ESLint
npm run format:check     # Prettier

# Build
npm run build            # Produção
npm run build:ssr        # Com SSR

# Deploy
vendor/bin/dep deploy production   # main → 10.1.7.76
vendor/bin/dep deploy develop      # developer → 10.1.7.75

# Docker
docker-compose up --build
```

## Tech Stack

| Camada       | Tecnologia                                                              |
|--------------|-------------------------------------------------------------------------|
| **Backend**  | PHP 8.2+, Laravel 12, Fortify (auth), LdapRecord (AD)                  |
| **Frontend** | Vue 3.5 (Composition API + `<script setup>`), TypeScript, Inertia.js 2 |
| **UI**       | Tailwind CSS 4, shadcn-vue (new-york-v4), Lucide icons, Reka UI        |
| **Build**    | Vite 7, SSR habilitado, Wayfinder (type-safe routes)                   |
| **Testes**   | Pest 4 (PHP), ESLint + Prettier (TS/Vue)                               |
| **Infra**    | Docker (Ubuntu 22.04, Nginx, PHP-FPM 8.4, Supervisor), Deployer       |

## Architecture

### Visão Geral

Aplicação Laravel + Inertia.js com duas áreas principais:

1. **Portal Público** — Cidadãos consultam emendas e parcerias (sem autenticação)
2. **Área Administrativa** — Gestores fazem CRUD de parlamentares, emendas, entidades, repasses e usuários (com autenticação e permissões)

### Autenticação Dual (Local + LDAP)

- Login aceita username ou email + password
- Primeiro tenta autenticação local (banco), depois tenta LDAP (Active Directory)
- Usuários LDAP são sincronizados automaticamente ao banco na primeira autenticação
- Campos LDAP sincronizados: `guid`, `domain`, `username`, `name`, `email`
- Suporte a 2FA (Two-Factor Authentication) via Fortify
- Configuração em `config/fortify.php` (username = 'username', home = '/admin/parlamentarians')

### Modelo de Permissões (3 flags booleanas no User)

| Flag                   | Acesso                                                       |
|------------------------|--------------------------------------------------------------|
| `is_admin`             | Acesso total (sistema + todos os módulos)                    |
| `can_manage_emendas`   | CRUD de parlamentares, emendas, minutas, importação/exportação |
| `can_manage_repasses`  | CRUD de entidades e repasses                                 |

Middleware correspondente: `AdminMiddleware`, `CheckEmendasAccess`, `CheckRepassesAccess`.

### Módulos do Sistema

#### Módulo Emendas

- **Parlamentarian** → tem muitas **Amendment** (hierarquia parent/child para sub-emendas)
- **Amendment** → tem muitas **ExecutionPhase** (fases de execução com status: pendente/em_andamento/concluído)
- **Amendment** → tem muitas **AmendmentFile** (tipos: plano_trabalho, prestacao_contas, comprovante)
- **Role** — tabela de referência para cargos políticos (Vereador, Deputado Federal, Senador, etc.)
- **Executor** / **Beneficiary** — tabelas de referência para órgãos executores e beneficiários
- **Minuta** — templates de documentos com upload de arquivo

#### Módulo Parcerias (Repasses)

- **Entity** → tem muitas **Transfer**
- **Transfer** → tem muitas **TransferFile** (mesmos tipos de arquivo: plano_trabalho, prestacao_contas, comprovante)

## Project Structure

```
app/
├── Actions/Fortify/         # CreateNewUser, ResetUserPassword, PasswordValidationRules
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # AmendmentController, ParlamentarianController, UserController,
│   │   │                    # EntityController, TransferController, AmendmentExportController,
│   │   │                    # AmendmentImportController, BeneficiaryController, ExecutorController,
│   │   │                    # RoleController
│   │   ├── Settings/        # ProfileController, PasswordController, TwoFactorAuthenticationController
│   │   ├── PageController.php             # Páginas públicas (home, emendas, parcerias, minutas)
│   │   ├── ParlamentarianPublicController.php  # Listagem/detalhe público de parlamentares
│   │   ├── AmendmentPublicController.php       # Listagem pública de emendas agrupadas
│   │   └── MinutaController.php               # CRUD de minutas (admin) + API pública
│   ├── Middleware/           # AdminMiddleware, CheckEmendasAccess, CheckRepassesAccess,
│   │                        # HandleAppearance, HandleInertiaRequests
│   └── Requests/            # ProfileUpdateRequest, TwoFactorAuthenticationRequest
├── Models/                  # User, Amendment, AmendmentFile, Parlamentarian, ExecutionPhase,
│                            # Entity, Transfer, TransferFile, Beneficiary, Executor, Role, Minuta
└── Providers/               # AppServiceProvider (HTTPS), FortifyServiceProvider (auth dual)

resources/js/
├── app.ts                   # Entry point (Inertia + Vue)
├── ssr.ts                   # SSR entry point
├── components/
│   ├── ui/                  # shadcn-vue components (alert, avatar, badge, button, card,
│   │                        # checkbox, collapsible, dialog, dropdown-menu, input, label,
│   │                        # navigation-menu, separator, sheet, sidebar, skeleton, spinner, tooltip)
│   ├── ComboBox.vue         # Autocomplete com criação inline (usado para executor/beneficiary/role)
│   ├── MinutasModal.vue     # Modal de minutas
│   ├── AppLogo.vue          # Logo do município
│   └── ...                  # AlertError, Heading, HeadingSmall, Icon, InputError, TextLink
├── composables/
│   ├── useAppearance.ts     # Tema claro/escuro/sistema
│   ├── useInitials.ts       # Gera iniciais de nomes (ex: "João Silva" → "JS")
│   └── useTwoFactorAuth.ts  # Workflow de configuração 2FA
├── layouts/
│   ├── AdminLayout.vue      # Sidebar com navegação por módulo (baseado em permissões)
│   ├── PublicLayout.vue     # Header azul (#0055a5) para portal de emendas
│   └── ParceriasLayout.vue  # Header verde (#059669) para portal de parcerias
├── lib/utils.ts             # cn() (tailwind-merge + clsx), urlIsActive(), toUrl()
├── pages/
│   ├── PortalHome.vue       # Página inicial — escolha entre Emendas e Parcerias
│   ├── WelcomeScreen.vue    # Portal de emendas — escolha: federal/estadual/municipal
│   ├── ParlamentariansList.vue   # Lista de parlamentares com filtros (ano, tipo, busca)
│   ├── ParlamentarianDetail.vue  # Detalhe do parlamentar com emendas agrupadas
│   ├── AmendmentsList.vue        # Lista agregada de emendas com filtros
│   ├── Parcerias.vue             # Portal de parcerias — listagem de entidades
│   ├── EntityDetail.vue          # Detalhe da entidade com repasses/transferências
│   ├── Minutas.vue               # Página pública de minutas (com admin inline)
│   ├── auth/Login.vue            # Login com username/password
│   └── admin/
│       ├── amendments/           # Index.vue (listagem/exportação/importação), Form.vue (CRUD)
│       ├── parlamentarians/      # Index.vue (listagem), Form.vue (CRUD com foto)
│       ├── entities/             # Index.vue (listagem), Form.vue (CRUD com logo)
│       ├── transfers/            # Index.vue (listagem), Form.vue (CRUD com arquivos)
│       ├── users/Index.vue       # Gestão de usuários + importação LDAP
│       └── Minutas/Index.vue     # Admin de minutas (upload/delete)
└── types/
    ├── index.d.ts            # Auth, User, NavItem, AppPageProps, BreadcrumbItem
    └── globals.d.ts          # Augmentações para Vite, Inertia, Vue

routes/
├── web.php                  # Rotas públicas (/, /emendas, /parcerias, /parlamentares, etc.)
├── admin.php                # Rotas admin (prefix: /admin, middleware: auth+admin)
└── console.php              # Comandos Artisan
```

## Routes

### Rotas Públicas (sem autenticação)

| Método | URI                    | Controller / Ação                    | Descrição                      |
|--------|------------------------|--------------------------------------|--------------------------------|
| GET    | `/`                    | PageController@home                  | Escolha: Emendas ou Parcerias  |
| GET    | `/emendas`             | PageController@emendas               | Portal de emendas (welcome)    |
| GET    | `/emendas/lista`       | AmendmentPublicController@index      | Emendas agrupadas por tipo     |
| GET    | `/parlamentares`       | ParlamentarianPublicController@index | Lista parlamentares            |
| GET    | `/parlamentar/{id}`    | ParlamentarianPublicController@show  | Detalhe do parlamentar         |
| GET    | `/parcerias`           | PageController@parcerias             | Portal de parcerias            |
| GET    | `/entidade/{entity}`   | PageController@entidade              | Detalhe da entidade            |
| GET    | `/minutas`             | PageController@minutas               | Minutas públicas               |
| GET    | `/api/minutas`         | MinutaController@index               | API JSON de minutas            |

### Rotas Admin (`/admin/*`, middleware: auth + admin)

| Módulo     | Recurso                | Middleware extra    | Operações                            |
|------------|------------------------|---------------------|--------------------------------------|
| Geral      | users                  | —                   | index, update, ldap/search, ldap/import |
| Emendas    | parlamentarians        | can.emendas         | CRUD completo (resource)             |
| Emendas    | amendments             | can.emendas         | CRUD + files, export (CSV/PDF), import |
| Emendas    | roles/executors/beneficiaries | can.emendas  | API para ComboBox (index + store)    |
| Emendas    | minutas                | can.emendas         | adminIndex, store, destroy           |
| Parcerias  | entities               | can.repasses        | CRUD completo (resource)             |
| Parcerias  | transfers              | can.repasses        | CRUD + files                         |

## Database Schema (Key Tables)

| Tabela             | Campos-chave                                                                      |
|--------------------|-----------------------------------------------------------------------------------|
| `users`            | name, username, email, password(?), guid(?), domain(?), is_admin, can_manage_emendas, can_manage_repasses, 2FA fields |
| `parlamentarians`  | name, role, photo_url, mandate_start, mandate_end, active                         |
| `amendments`       | parlamentarian_id, parent_amendment_id(?), number, year, description, expense_object, expected_value, executor, beneficiary |
| `execution_phases` | amendment_id, phase, expected_date, status (enum: pendente/em_andamento/concluido) |
| `amendment_files`  | amendment_id, type, file_url, file_name                                           |
| `entities`         | name, type, cnpj, address, phone, email, website, description, logo_url, active   |
| `transfers`        | entity_id, object, year, value, active                                            |
| `transfer_files`   | transfer_id, type (enum: plano_trabalho/prestacao_contas/comprovante), file_url, file_name |
| `roles`            | name (unique) — cargos políticos                                                  |
| `executors`        | name (unique) — órgãos executores                                                 |
| `beneficiaries`    | name (unique) — municípios/entidades beneficiárias                                |
| `minutas`          | title, description, file_path, user_id                                            |

## Conventions

### Backend (PHP/Laravel)

- **PHP 8.2+** com tipagem estrita onde possível
- **Laravel 12** — seguir convenções do framework (Eloquent, Form Requests, Resource Controllers)
- **Pest** para testes — preferir testes de feature sobre unit
- **Pint** para formatação PHP (`vendor/bin/pint`)
- Controllers Admin vivem em `App\Http\Controllers\Admin\`
- Controllers públicos na raiz de `App\Http\Controllers\`
- Inertia::render() mapeia diretamente para o path da página Vue (ex: `'admin/amendments/Index'`)
- Upload de arquivos usa o disco `public` (storage/app/public) — caminhos relativos no banco
- Valores monetários são `decimal:2` no cast e `numeric|min:0` na validação
- Tipo de arquivo usa enum string em migration (plano_trabalho, prestacao_contas, comprovante)

### Frontend (Vue/TypeScript)

- **Vue 3.5** com Composition API e `<script setup lang="ts">` exclusivamente
- **TypeScript** — evitar `any` (regra desabilitada no ESLint, mas preferir tipos explícitos)
- **Inertia.js** — navegação via `router.get()` / `router.post()`, dados via `defineProps`
- **shadcn-vue** (new-york-v4) — componentes UI em `components/ui/`; não editar manualmente
- **Tailwind CSS 4** — usar classes utilitárias, design tokens via CSS variables em `app.css`
- **Lucide** para ícones — importar de `lucide-vue-next`
- **ComboBox.vue** — autocomplete reutilizável com criação inline (executor, beneficiary, role)
- Path alias: `@/` → `resources/js/`
- Layouts definidos por página (AdminLayout, PublicLayout, ParceriasLayout), ou `layout: null` para custom
- Formatação: `npm run format` (Prettier com organize-imports + tailwindcss plugins)
- Linting: `npm run lint` (ESLint com vue/essential + TypeScript recommended)

### Padrões Recorrentes

- **Hierarquia de emendas**: emendas com mesmo `number` + `year` vinculam como parent_amendment_id
- **Upload multi-tipo**: 3 categorias fixas (plano_trabalho, prestacao_contas, comprovante) para amendments e transfers
- **Filtro por tipo de parlamentar**: `federal` (Deputado Federal/Senador), `estadual` (Deputado Estadual), `municipal` (Vereador)
- **Filtro por ano**: baseado em mandate_start/mandate_end ou year da emenda
- **ComboBox com criação inline**: envia POST para criar novo registro e adiciona ao dropdown
- **Exportação**: CSV com UTF-8 BOM, PDF via DomPDF (landscape A4)
- **Importação CSV**: preview → validação → import em batch

## File Storage

| Diretório         | Conteúdo                            | Disco   |
|-------------------|-------------------------------------|---------|
| `storage/app/public/fotos/`     | Fotos de parlamentares    | public  |
| `storage/app/public/logos/`     | Logos de entidades        | public  |
| `storage/app/public/minutas/`   | Documentos/minutas        | public  |
| `storage/app/public/repasses/`  | Arquivos de repasses      | public  |
| `public/VEREADORES/`            | Fotos dos vereadores (seeder) | static |
| `public/DEPUTADOS FEDERAIS/`    | Fotos deputados federais  | static  |
| `public/DEPUTADOS ESTADUAIS/`   | Fotos deputados estaduais | static  |
| `public/ENTIDADES/`             | Logos de entidades (seeder) | static |

## Deployment

- **Deployer** (`deploy.php`) com recipe Laravel
- **Production**: `main` → `10.1.7.76:/var/www/emendas`
- **Develop**: `developer` → `10.1.7.75:/var/www/emendas`
- **Docker**: Ubuntu 22.04, Nginx, PHP-FPM 8.4, Supervisor, Node.js 20.x
- **Volumes compartilhados**: `storage/` e `bootstrap/cache/` via bind mounts
- **Upload limits**: 100MB (PHP + Nginx), 50 arquivos simultâneos
- **Rede Docker**: `rede_dados_geral` (externa)

## Common Pitfalls

- Passwords são nullable (usuários LDAP não têm senha local)
- O primeiro usuário criado via seeder vira admin (`AdminUserSeeder`)
- Componentes `ui/` são gerados pelo shadcn-vue — não editar diretamente
- Frontend usa `layout: null` em páginas com layout customizado (PortalHome, WelcomeScreen, Parcerias, Login)
- Valores monetários chegam como string do formulário — converter para float no backend
- CSV de importação precisa de BOM para Excel abrir com UTF-8 correto
- O arquivo `deploy.php` espera chave SSH configurada para o usuário `deploy`
- `HandleInertiaRequests` compartilha uma citação inspiracional aleatória (`quote`) em todas as páginas
