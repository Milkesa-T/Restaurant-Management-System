# 🍽️ Restaurant Management System

A full-featured, multi-branch restaurant management platform built with **Laravel 11**, **Vue 3**, **Inertia.js**, and **MySQL**.

### Features
- 🔐 Role-based access control (Super Admin, Branch Manager, Waiter, Chef, Cashier)
- 📱 QR code digital menu & customer self-ordering
- 🍳 Kitchen Display System (KDS)
- 💳 POS billing with split payments
- 📦 Inventory & recipe-based stock deduction (configurable trigger)
- 📊 Sales, SLA, and staff performance analytics
- 💸 Expense management with approval workflow
- 🔔 Real-time notifications
- 💰 Chapa payment gateway integration *(planned — Phase 5)*

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 |
| Frontend | Vue 3 (Composition API) + Inertia.js |
| Styling | Tailwind CSS |
| Database | MySQL 8.0+ |
| Auth | Laravel Breeze |
| Real-time | Laravel Reverb (WebSockets) |
| Payment | Chapa *(coming soon)* |

---

## 🚀 Local Setup

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL 8.0+
- Git

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_ORG/restaurant-rms.git
cd restaurant-rms

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy env file and configure
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Create MySQL database
# Open MySQL and run:
#   CREATE DATABASE restaurant_rms;

# 7. Update .env with your local DB credentials
#   DB_DATABASE=restaurant_rms
#   DB_USERNAME=root
#   DB_PASSWORD=your_password

# 8. Run migrations and seed sample data
php artisan migrate --seed

# 9. Start the dev servers (run in two terminals)
php artisan serve
npm run dev
```

### Default Login (after seeding)
| Role | Email | Password |
|---|---|---|
| Super Admin | admin@restaurant-rms.com | password |
| Branch Manager | manager@restaurant-rms.com | password |
| Waiter | waiter@restaurant-rms.com | password |

---

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/          # Super Admin controllers
│   ├── BranchManager/  # Branch Manager controllers
│   ├── Waiter/
│   ├── Kitchen/
│   ├── POS/
│   ├── Inventory/
│   └── Customer/       # Public QR menu
├── Models/             # Eloquent models
│   └── Traits/         # HasDynamicSettings, HasBranchScope
└── Services/
    ├── OrderService.php
    ├── InventoryService.php
    └── AnalyticsService.php

resources/js/
├── Pages/
│   ├── Admin/
│   ├── Manager/
│   ├── Waiter/
│   ├── Kitchen/
│   ├── POS/
│   ├── Customer/       # Public digital menu
│   └── Reports/
├── Layouts/
└── Composables/
```

---

## 🌿 Git Branch Strategy

| Branch | Purpose |
|---|---|
| `main` | Production-ready code only |
| `develop` | Active development, all PRs merge here |
| `feature/xxx` | Individual feature branches |
| `fix/xxx` | Bug fix branches |

### Workflow
```bash
# Start a new feature
git checkout develop
git pull origin develop
git checkout -b feature/waiter-dashboard

# When done, push and open a Pull Request → develop
git push origin feature/waiter-dashboard
```

> ⚠️ **Never push directly to `main`.** All changes go through a Pull Request.

---

## ⚙️ Configurable Settings (per Branch)

Some behaviors are controlled via `branch_settings` table (not code):

| Key | Values | Description |
|---|---|---|
| `inventory_deduction_trigger` | `on_approval` / `on_served` / `on_paid` | When to deduct stock |
| `sla_waiter_approval_minutes` | integer | SLA target for order approval |
| `sla_kitchen_prep_minutes` | integer | SLA target for kitchen preparation |
| `tax_rate` | decimal | Branch-level tax percentage |
| `receipt_footer` | string | Custom text on printed receipts |

---

## 💰 Chapa Integration (Phase 5 — Pending)

Chapa will be integrated after the core system reaches ~90% completion.

**What's already prepared:**
- `.env.example` has `CHAPA_SECRET_KEY`, `CHAPA_PUBLIC_KEY`, `CHAPA_WEBHOOK_SECRET`
- `payment_methods` table supports `type = 'online'` for Chapa
- `payments.reference_number` stores Chapa transaction IDs
- Webhook endpoint will be at `POST /webhooks/chapa`

**To enable when ready:**
1. Register at [dashboard.chapa.co](https://dashboard.chapa.co)
2. Add your keys to `.env`
3. Run: `php artisan chapa:install` *(to be created in Phase 5)*

---

## 🧪 Running Tests

```bash
php artisan test
```

Key test suites:
- `OrderFlowTest` — full order lifecycle
- `InventoryDeductionTest` — stock deduction per trigger setting
- `PermissionTest` — role access enforcement

---

## 👥 Team

| Name | Role |
|---|---|
| *(your name)* | Project Lead |
| *(team member)* | Backend Developer |
| *(team member)* | Frontend Developer |

---

## 📄 License

Proprietary — Internal use only.
