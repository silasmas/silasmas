# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`silasmas` is a **dual-stack app**: a Laravel 10 (PHP) API/admin backend at the repo root, plus a separate Next.js 16/React 19/TypeScript frontend in `frontend/`. They communicate over REST; CORS is configured for `localhost:3000` and `silasmas.com` (`config/cors.php`, driven by `FRONTEND_URL`/`CORS_ALLOWED_ORIGINS`).

Note: this repo's `frontend/` is a separate, likely-legacy checkout distinct from the standalone `silasmas-web` repo elsewhere on this machine — both appear to be Next.js clients for the same Laravel API. Don't assume they're in sync; check which one is actually deployed before editing.

Two business domains coexist in the backend: an older **"SDev" portfolio/CRM** domain (Project/Role/Status/User/Website/Message) and the actively-developed **"SDev Academy"** domain (Student, TrainingSession, Registration, SessionPayment, AcademyEmailTemplate) — a coding bootcamp with paid registration.

## Commands

**Backend (root):**
```bash
composer install                # post-install auto-copies .env.example, key:generate, filament:upgrade
php artisan serve
php artisan migrate
php artisan db:seed             # creates Filament admin from ADMIN_EMAIL/ADMIN_PASSWORD env vars
php artisan test                # or vendor/bin/phpunit
php artisan test --filter=test_login_screen_can_be_rendered   # single test
vendor/bin/pint                 # code style (installed, not wired into composer scripts)
npm run dev / npm run build     # proxies to frontend/ via --prefix; dev:legacy-vite/build:legacy-vite still exist for the old Blade/Vite asset pipeline
```

**Frontend (`frontend/`):**
```bash
npm install
npm run dev      # kills port 3000 first (predev), then next dev
npm run build
npm run start
npm run lint
```
`frontend/AGENTS.md` warns this Next.js version has breaking changes vs. training data — check `node_modules/next/dist/docs/` before writing Next.js code there. `frontend/CLAUDE.md` just references `@AGENTS.md`.

## Architecture (backend)

- **Routes**: `routes/web.php` — homepage, a `/deploy/*` toolchain (secret-protected migrate/seed/storage-link for SSH-less Hostinger deploys, see `config/deploy.php`), FlexPay payment callback/return redirects into the Next.js frontend, Filament mounted at `/admin`. `routes/api.php` — legacy CRUD (`message`, `project`, `role`, `status`, `user`, `website`, duplicated as both `apiResource` and manual `Route::resource` + search endpoints) plus the newer Academy public API (`/api/academy/*`: sessions, registration, pre-registration, payment processing/confirmation, participant space by token), `/api/contact`, `/api/site`, `/api/analytics/track`. `routes/auth.php` — Breeze-style session auth, reused by Filament's login.
- **Controllers**: `Http/Controllers/API/` (JSON, extend shared `BaseController` with `handleResponse`/`handleError`), `Http/Controllers/API/Academy/` (business-critical registration/payment flows), `Http/Controllers/Web/` (Home, Admin, Deploy), `Http/Controllers/Auth/` (Breeze).
- **Services** (`app/Services/`): `FlexPayService`/`FlexPayCheckService` (DRC Mobile Money + card payment gateway), `AcademyRegistrationMailer`/`Notifier`/`PaymentFailureNotifier`/`PreRegistrationNotifier`, `AcademyEmailTemplateRenderer`/`PreviewRenderer` (DB-driven email templates), `RegistrationPdfExporter` (dompdf), `Deploy/MigrationRunnerService`.
- **Support** (`app/Support/`): `CurrencyConverter` (`USD_TO_CDF_RATE`), `MobileMoneyValidation`/`Operators`, `ParticipantToken`, `FrontendUrl`, `MediaUrl` (YouTube/Vimeo embeds), `AcademyPaymentPricing`.
- **Auth**: Breeze session auth for web/Filament; Sanctum (`HasApiTokens`) for API tokens.
- **Admin panel**: Filament v3 at `/admin` (amber theme). Resources cover both the legacy domain and Academy domain (TrainingSession with a pre-registrations RelationManager, Registration, SessionPayment, Student, AcademyEmailTemplate, SiteBlock, SiteSetting, SiteVisit). Custom pages: `RunMigrations` (run DB migrations from the UI), `SiteAnalytics`. Widgets for failed payments and visit stats/charts.
- **Key integrations**: FlexPay (M-Pesa, Airtel Money, Orange Money, AfriMoney, cards), AWS S3 (`docs/aws-s3-cors.example.json`), Maatwebsite Excel exports, dompdf for registration PDFs, SMTP mail (Mailpit locally), optional SMS/WhatsApp webhooks for Academy reminders.
- **Data model**: `students`, `training_sessions`, `registrations` (unique per student+session), then pricing/currency/FlexPay payment tracking, pre-registration windows, `site_visits` analytics, CMS-like `site_blocks`/`site_settings`, `academy_email_templates`.
- No `lang/` localization directory exists, though code/comments are French throughout (DRC-based project).

## Frontend (`frontend/`)

Blade views in the root app exist only for auth/Filament/emails/PDF export/legacy portfolio pages — no Livewire/Inertia/Vue. The real customer-facing site is the Next.js/React/TypeScript app in `frontend/`, styled with its own Tailwind v4 config (separate from the root's Tailwind v3 used for legacy Blade views).

## Testing gaps

`tests/Feature/` is mostly Breeze auth scaffolding (Authentication, EmailVerification, PasswordConfirmation/Reset/Update, Registration, Profile) plus a generic `ExampleTest`. **No dedicated tests exist for the Academy or FlexPay business logic** — treat changes there as higher-risk and consider adding coverage alongside fixes.
