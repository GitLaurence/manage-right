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
- [x] Shift templates (name, start time, end time, break duration)
- [x] Weekly schedule builder per branch
- [x] Drag-and-drop schedule assignment
- [x] Schedule publishing and employee notifications

---

### Phase 4 — Selfie Time-In / Time-Out
- [x] Camera capture UI (mobile-friendly Livewire component)
- [x] Selfie upload to Supabase Storage
- [x] Time-in / time-out logging with timestamp and geolocation (optional)
- [x] Manager review interface for attendance entries
- [x] Late / undertime auto-detection based on assigned schedule

---

### Phase 5 — Employee Requests
- [x] Leave request form (type, dates, reason)
- [x] Overtime request form (date, hours, reason)
- [x] Undertime request form
- [x] Manager approval / rejection workflow with remarks
- [x] Request status tracking for employees

---

### Phase 6 — Reports & Payroll Data
- [x] Daily attendance summary per branch
- [x] Weekly attendance report per employee
- [x] Overtime, undertime, late, and absence tallies
- [x] Exportable report (CSV) for payroll processing
- [x] Dashboard overview cards (total present, late, absent, on leave)

---

### Phase 7 — Multi-Branch Dashboard
- [x] Owner-level view across all branches
- [x] Per-branch headcount and attendance snapshot
- [x] Branch-level manager permissions
- [x] Activity logs for audit trail

---

### Phase 8 — Polish & Launch
- [x] Mobile-responsive UI review
- [x] Performance optimization (lazy loading, caching)
- [x] Supabase Row Level Security (RLS) policies
- [x] End-to-end testing with Pest
- [x] Deployment setup (Render, Docker)

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

## User Guide

This section is for business owners and managers using Manage Right day-to-day. No technical knowledge required.

---

### Getting Started

#### 1. Create your account
Go to the app URL and click **Get started**. Enter your name, email address, and a password, then click **Register**.

#### 2. Set up your business
After registering you will be taken to a setup screen. Enter your business name and select your business type (café, salon, retail, etc.). You will also create your first branch — give it a name and select the timezone it operates in.

#### 3. Add employees
Go to **Employees → Invite Employee** in the left sidebar. Enter the employee's email address, choose their role (Manager or Employee), and select which branch they belong to. Click **Create Invitation** and copy the link that appears. Send that link to your employee by chat or email.

When the employee opens the link and registers, they will automatically be added to your business.

---

### For Managers

#### Reviewing attendance
Go to **Attendance → Review** in the sidebar. You will see every clock-in and clock-out for the selected date and branch. Click **Approve** to confirm an entry, or **Flag** to mark it for follow-up and leave a note.

Use the date picker at the top to look back at previous days.

#### Approving requests
Go to **Requests → Approvals**. You will see all pending leave, overtime, and undertime requests from your team. Click **Approve** to accept a request, or **Reject** to decline it and optionally leave a note explaining why.

#### Viewing reports
- **Reports → Attendance** — shows a daily breakdown of who was present, late, or absent for a selected date.
- **Reports → Payroll Summary** — shows a week-by-week summary per employee including days present, late hours, undertime, overtime, and approved leave days. Use the **Export CSV** button to download the data for payroll processing.

---

### For Employees

#### Clocking in and out
Go to **Clock In / Out** in the sidebar. Allow the camera when prompted, take a selfie, and click **Record Time-In**. At the end of your shift, return to the same page and click **Record Time-Out**.

> Tip: Use your phone's browser for the best camera experience.

#### Submitting a request
Go to **Requests → My Requests** and click **New Request**. Choose the type:

| Type | When to use |
|------|-------------|
| Leave | Sick days, vacation, emergency, or unpaid leave |
| Overtime | You worked beyond your scheduled end time |
| Undertime | You left before your scheduled end time |

Fill in the dates, hours (if applicable), and the reason, then click **Submit**. Your manager will be notified and you can track the status from the same page.

---

### Scheduling

#### Creating shift templates
Go to **Shift Templates** and click **New Template**. Give it a name (e.g., "Morning Shift"), set the start time, end time, and break duration. You can create as many templates as your business needs.

#### Building the weekly schedule
Go to **Schedule**. Select the branch and the week you want to plan. Assign a shift template to each employee for each day. When you are done, publish the schedule — employees will see their assigned shifts when they clock in.

---

### Dashboard

The **Dashboard** shows a live snapshot of today across all your branches:

- **Present** — number of employees who have clocked in today
- **Late** — employees who clocked in after their scheduled start time
- **Absent** — employees with a scheduled shift but no clock-in yet
- **On Leave** — employees with an approved leave request for today
- **Pending Requests** — leave and overtime requests waiting for approval
- **Recent Activity** — a log of the last actions taken in your account

---

### Frequently Asked Questions

**Can I manage more than one branch?**
Yes. You can add multiple branches from **Branches** in the sidebar. Each branch has its own schedule, attendance log, and reports.

**What happens if an employee forgets to clock out?**
Their record will show a time-in with no matching time-out. A manager can see this in **Attendance → Review** and flag it for correction.

**Can I use Manage Right on a phone?**
Yes. The app works in any modern mobile browser. The Clock In page is designed specifically for phones.

**How do I export data for payroll?**
Go to **Reports → Payroll Summary**, select the week and branch, then click **Export CSV**. Open the file in Excel or Google Sheets.

**How do I remove an employee?**
Employee management (deactivating accounts) is coming in a future update. For now, contact your system administrator.

---

## License

MIT
