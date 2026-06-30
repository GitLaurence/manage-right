# Manage Right

> Employee management made simple for small businesses.

Manage Right is built for cafés, restaurants, salons, retail stores, kiosks, clinics, and other small businesses that want a smarter, paperless way to manage their workforce — from scheduling and attendance to payroll-ready reports.

---

## Features

| Feature | Description |
|---|---|
| 📸 Selfie Time-In / Time-Out | Employees clock in and out using a selfie for easy attendance verification |
| 📅 Employee Scheduling | Create and manage work schedules across multiple locations |
| 💰 Payroll-Ready Reports | View attendance, late arrivals, undertime, overtime, and absences at a glance |
| 🏢 Multi-Branch Management | Monitor employees from different branches in one dashboard |
| 📝 Employee Requests | Handle leave, overtime, and undertime requests online |
| 📊 Weekly Reports | Organized attendance records without paper logbooks or group chats |

---

## Tech Stack

- **Backend** — [Laravel 12](https://laravel.com)
- **Frontend** — [Livewire 3](https://livewire.laravel.com) + [Flux UI](https://fluxui.dev)
- **Database / Storage** — [Supabase](https://supabase.com) (PostgreSQL + Storage for selfie uploads)
- **Auth** — Laravel Fortify (with 2FA and Passkey support)

---

## Implementation Plan

### Phase 1 — Foundation
- [x] Laravel project scaffold with Livewire, Fortify, and Flux UI
- [x] Supabase database connection configured
- [x] Authentication flows: register, login, 2FA, passkey, password reset
- [x] User settings: profile, security, appearance, account deletion
- [x] Role system: `Owner`, `Manager`, `Employee`
- [x] Multi-tenancy: businesses and branches as top-level scopes

---

### Phase 2 — Business & Branch Setup
- [x] Business registration flow (name, type, logo)
- [x] Branch creation and management (name, address, timezone)
- [x] Invite employees by email and assign them to a branch
- [x] Employee profiles (position, employment type, rate type)

---

### Phase 3 — Scheduling
- [ ] Shift templates (name, start time, end time, break duration)
- [ ] Weekly schedule builder per branch
- [ ] Drag-and-drop schedule assignment
- [ ] Schedule publishing and employee notifications

---

### Phase 4 — Selfie Time-In / Time-Out
- [ ] Camera capture UI (mobile-friendly Livewire component)
- [ ] Selfie upload to Supabase Storage
- [ ] Time-in / time-out logging with timestamp and geolocation (optional)
- [ ] Manager review interface for attendance entries
- [ ] Late / undertime auto-detection based on assigned schedule

---

### Phase 5 — Employee Requests
- [ ] Leave request form (type, dates, reason)
- [ ] Overtime request form (date, hours, reason)
- [ ] Undertime request form
- [ ] Manager approval / rejection workflow with remarks
- [ ] Request status tracking for employees

---

### Phase 6 — Reports & Payroll Data
- [ ] Daily attendance summary per branch
- [ ] Weekly attendance report per employee
- [ ] Overtime, undertime, late, and absence tallies
- [ ] Exportable report (CSV / PDF) for payroll processing
- [ ] Dashboard overview cards (total present, late, absent, on leave)

---

### Phase 7 — Multi-Branch Dashboard
- [ ] Owner-level view across all branches
- [ ] Per-branch headcount and attendance snapshot
- [ ] Branch-level manager permissions
- [ ] Activity logs for audit trail

---

### Phase 8 — Polish & Launch
- [ ] Mobile-responsive UI review
- [ ] Performance optimization (lazy loading, caching)
- [ ] Supabase Row Level Security (RLS) policies
- [ ] End-to-end testing with Pest
- [ ] Deployment setup (Forge / Coolify / Railway)

---

## Local Setup

```bash
# Clone the repo
git clone https://github.com/GitLaurence/manage-right.git
cd manage-right

# Install dependencies
composer install
npm install

# Copy environment file and configure Supabase credentials
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start dev servers
php artisan serve
npm run dev
```

### Required `.env` values

```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password

SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your-anon-key
```

---

## License

MIT
