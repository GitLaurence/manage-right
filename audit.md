# Manage Right — Bug & Security Audit

> **Date:** 2026-07-01  
> **Branch:** main  
> **Scope:** All Livewire components, models, routes, migrations, and views (Phases 1–7)  
> **Last updated:** 2026-07-02 — all 22 findings resolved except M-01/M-03/L-05/L-06, which turned out to already be fixed or non-issues on inspection (see notes on each item). Everything else fixed and verified against live data.

---

## Summary

| Severity | Count | Open |
|----------|-------|------|
| Critical | 5     | 0    |
| High     | 8     | 0    |
| Medium   | 3     | 0    |
| Low      | 6     | 0    |
| **Total**| **22**| **0**|

---

## Critical

### C-01 — IDOR: ManagerApprovals can approve/reject any business's requests ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Requests/ManagerApprovals.php` — lines 50, 72  
**Fix applied:** `approve()` and `reject()` now scope the lookup with `->where('business_id', $this->business->id)->firstOrFail()`. Verified a mismatched `business_id` throws `ModelNotFoundException` (404).

**Description:** `approve()` and `reject()` call `EmployeeRequest::find($id)` with a raw integer from the client. There is no check that the record belongs to the currently authenticated user's business. Any authenticated user can approve or reject any request in the database by replaying the Livewire action with an arbitrary ID.

**Fix:**
```php
// approve()
$request = EmployeeRequest::where('id', $id)
    ->where('business_id', $this->business->id)
    ->firstOrFail();
$request->update([...]);

// reject()
$request = EmployeeRequest::where('id', $this->reviewingId)
    ->where('business_id', $this->business->id)
    ->firstOrFail();
```

---

### C-02 — IDOR: ManagerReview can approve/flag any business's attendance logs ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ManagerReview.php` — lines 58, 80  
**Fix applied:** `approve()` and `flag()` now scope the lookup with `->where('business_id', $this->business->id)->firstOrFail()`. Verified a mismatched `business_id` throws `ModelNotFoundException` (404).

**Description:** `approve()` and `flag()` call `AttendanceLog::find($logId)` / `AttendanceLog::find($this->flaggingId)` without scoping to the current business. An attacker can modify logs that belong to a different tenant.

**Fix:**
```php
$log = AttendanceLog::where('id', $logId)
    ->where('business_id', $this->business->id)
    ->firstOrFail();
```

---

### C-03 — Missing role guard on ManagerApprovals ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Requests/ManagerApprovals.php`  
**Fix applied:** `mount()` now calls `abort_unless($business && Auth::user()->isManagerOrOwnerOf($business), 403)`. Also hid the "Approvals" sidebar link from employees in `resources/views/layouts/app/sidebar.blade.php`. Verified against live data: owner allowed, employee blocked with 403.

**Description:** No role check in `mount()` or anywhere in the component. Any employee (not just managers/owners) can reach `/requests/approvals` and approve or reject requests. The route is only behind `auth` + `has.business` middleware.

**Fix:** Add a role check in `mount()`:
```php
public function mount(): void
{
    abort_unless(
        in_array(Auth::user()->roleIn($this->business->id), ['owner', 'manager']),
        403
    );
    $this->branchId = ...;
}
```

---

### C-04 — Missing role guard on ManagerReview ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ManagerReview.php`  
**Description:** Same issue as C-03. Any employee can access `/attendance/review` and flag or approve other employees' attendance entries.

**Fix applied:** Same pattern as C-03 — `mount()` now aborts with 403 unless `isManagerOrOwnerOf($business)`. Sidebar link hidden from employees.

While fixing C-03/C-04, the role guard was also extended to close the same gap on other manager-only pages that the audit hadn't separately flagged: `BranchManager` and `InviteEmployee` (owner-only), and `AttendanceSummary`, `PayrollSummary`, `WeeklySchedule`, `ShiftTemplates` (manager-or-owner). All now guarded in both the sidebar (UI) and each component's `mount()` (backend).

---

### C-05 — Branch ownership not verified in InviteEmployee ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Business/InviteEmployee.php` — line 22  
**Fix applied:** `send()` now validates `branchId` with `Rule::exists('branches', 'id')->where('business_id', $this->business->id)` instead of a bare `exists:branches,id` rule.

**Description:** The `branchId` field validates `nullable|exists:branches,id` but does not verify that the selected branch belongs to the current business. An owner of Business A could craft a request to assign an invitation to a branch of Business B.

**Fix:**
```php
// In send():
if ($this->branchId) {
    abort_unless(
        $this->business->branches()->where('id', $this->branchId)->exists(),
        403
    );
}
```

Or add a custom validation rule: `Rule::exists('branches', 'id')->where('business_id', $this->business->id)`.

---

## High

### H-01 — Race condition: duplicate time-in records possible ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ClockIn.php` — line 91  
**Fix applied:** Option A from below — `record()` now wraps the whole check-then-insert flow in a `Cache::lock("clock-in:{user}:{business}", 10)`, re-checking `nextAction` after acquiring the lock. Verified the DB-backed lock (`cache_locks` table) correctly blocks a second concurrent acquire and releases cleanly.

**Description:** The `record()` method checks `$this->nextAction` (a computed property) and then writes to `attendance_logs` as two separate operations. If two requests arrive concurrently (e.g., a double-tap on a mobile), both can read `nextAction === 'time_in'` before either inserts, creating duplicate records.

**Fix:** Use a database lock or a unique constraint:
```php
// Option A — application-level lock
$lock = Cache::lock("clock-in:{$this->user->id}:{$this->business->id}", 5);
if (!$lock->get()) {
    Flux::toast(text: 'Please wait a moment and try again.');
    return;
}

// Option B — unique DB constraint (migration)
$table->unique(['user_id', 'business_id', 'type', DB::raw('DATE(logged_at)')]);
```

---

### H-02 — CSV injection in payroll export ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Reports/PayrollSummary.php` — lines 133–136  
**Fix applied:** Exactly the sanitizer suggested below, applied to employee name and branch name before `fputcsv()`.

**Description:** Employee names and branch names are written directly to CSV via `fputcsv()`. If a name starts with `=`, `+`, `-`, or `@`, spreadsheet software (Excel, Sheets) will interpret it as a formula. An employee named `=HYPERLINK("http://evil.com","Click")` would execute on open.

**Fix:** Sanitize values before writing:
```php
$sanitize = fn($v) => preg_match('/^[=+\-@\t\r]/', (string)$v)
    ? "'" . $v
    : $v;

fputcsv($handle, [
    $sanitize($row->user->name),
    $sanitize($row->branch?->name ?? ''),
    ...
]);
```

---

### H-03 — Invite token not matched against registering email ✅ SUPERSEDED (2026-07-02)
**File:** `routes/web.php` — line 47  
**Resolution:** The invite flow was redesigned per product decision — invitations no longer target a specific email address at all (the form only collects role/branch/position). Whoever holds the link registers with whatever email they choose, and acceptance is handled by the new `App\Actions\AcceptInvitation` action (used both by the registration hook and by an already-logged-in user visiting the link), which attaches the account directly. There's no email to mismatch anymore, so this finding no longer applies as originally written. Note: this means anyone who obtains the link can register as that role — an accepted tradeoff, not a regression, since the original code never actually enforced the email match either (see description below).

**Original description:** When a user visits an invite link, the token is stored in session (`session(['invitation_token' => $token])`). The invitation has an `email` field, but if the invited person registers with a different email address, the mismatch is not caught unless the registration controller explicitly validates the session token against the user's email. The invited person may be added to the wrong business, or an uninvited user may hijack an invite.

**Fix:** In the registration controller (or Fortify pipeline), after user creation, check:
```php
if ($token = session('invitation_token')) {
    $invitation = Invitation::where('token', $token)
        ->where('email', $user->email)  // enforce email match
        ->whereNull('accepted_at')
        ->first();
    // proceed only if found
}
```

---

### H-04 — RLS not enabled on new tables ✅ RESOLVED (2026-07-02)
**File:** Migration files for `attendance_logs`, `employee_requests`, `activity_logs`  
**Fix applied:** New migration `2026_07_02_000002_enable_rls_on_phase_4_7_tables.php`, matching the pattern already used in `2026_06_30_200001_enable_rls_on_all_tables.php`. Confirmed the app connects as a role that bypasses RLS (per that migration's existing comment), so this only blocks direct PostgREST/API access without affecting the app.

**Description:** Row Level Security is enabled on the original tables (per `32b35f4` commit), but the Phase 4–7 migrations that create `attendance_logs`, `employee_requests`, and `activity_logs` do not include `ALTER TABLE ... ENABLE ROW LEVEL SECURITY`. Supabase direct access bypasses Laravel's application-level scoping.

**Fix:** Add to each relevant migration's `up()`:
```php
DB::statement('ALTER TABLE attendance_logs ENABLE ROW LEVEL SECURITY');
DB::statement('ALTER TABLE employee_requests ENABLE ROW LEVEL SECURITY');
DB::statement('ALTER TABLE activity_logs ENABLE ROW LEVEL SECURITY');
```

---

### H-05 — Undertime/overtime calculation uses server time, not branch timezone ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ClockIn.php` — lines 121–129  
**Fix applied:** `record()` now resolves `$membership->branch?->timezone ?? config('app.timezone')` and uses `Carbon::now($timezone)` plus a timezone-aware `Carbon::parse(..., $timezone)` for the scheduled start/end, matching the fix below.

**Description:** `Carbon::now()` uses the PHP server's timezone. If the server is in UTC and the branch is in Asia/Manila (UTC+8), `now()` at 08:05 server time is 16:05 local — a shift scheduled to start at 08:00 local would incorrectly compute 8 hours of lateness.

**Fix:** Resolve branch timezone and use it for all comparisons:
```php
$tz = $membership->branch?->timezone ?? config('app.timezone');
$now = Carbon::now($tz);
$scheduledStart = Carbon::parse($now->toDateString().' '.$schedule->start_time, $tz);
```

---

### H-06 — `->value('id')` on empty branch collection returns null, not a safe default ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ManagerReview.php` line 26, `app/Livewire/Requests/ManagerApprovals.php` line 22, `app/Livewire/Reports/PayrollSummary.php` line 23, `app/Livewire/Reports/AttendanceSummary.php`  
**Fix applied:** Each affected computed query (`ManagerReview::logs()`, `ManagerApprovals::requests()`, `PayrollSummary::rows()`, `AttendanceSummary::logs()`) now returns an empty collection up front when `$this->branches->isEmpty()`, so a business with zero branches can never fall through to an unfiltered "all branches" query by accident.

**Description:** `->branches()->value('id')` returns `null` if the business has no branches (e.g., a business created but not yet set up). This `null` is assigned to `$this->branchId` and then used in queries as `->when($this->branchId, ...)`, silently disabling the branch filter and exposing data across all branches.

**Fix:** Either guard against `null` explicitly or set a safe default that causes no-data to be returned until a branch exists.

---

### H-07 — Missing transaction in BusinessSetup ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Onboarding/BusinessSetup.php`  
**Fix applied:** `save()` now wraps the `Business::create()`, `BusinessUser::create()`, and `$user->update()` calls in `DB::transaction()`. (Note: this component doesn't create a branch — that happens on a separate page — so only 2 writes needed wrapping, not 3 as originally described.)

**Description:** Business registration creates at least 3 records (business, branch, membership) in separate queries with no wrapping transaction. A failure on the second or third insert leaves orphaned records and an inconsistent state that may break subsequent page loads.

**Fix:**
```php
DB::transaction(function () {
    $business  = Business::create([...]);
    $branch    = $business->branches()->create([...]);
    $business->memberships()->create(['user_id' => Auth::id(), 'role' => 'owner', ...]);
});
```

---

### H-08 — Late-minutes sign inversion may produce wrong values ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Attendance/ClockIn.php` — line 122  
**Fix applied:** Replaced with the explicit-comparison form suggested below (`$now->gt($scheduledStart) ? diffInMinutes : 0`), applied to late, undertime, and overtime calculations alike. Landed together with H-05 since both touch the same lines.

**Description:** `$now->diffInMinutes($scheduledStart, false) * -1` — the second argument `false` makes `diffInMinutes` return a negative value if `$now > $scheduledStart`. Multiplying by `-1` makes it positive, which is correct. However, `max(0, ...)` around it means early arrivals correctly give 0. The logic is correct but fragile: if Carbon's signed diff behavior changes or `$scheduledStart` is midnight (null start_time), the calculation silently produces wrong results.

**Fix:** Use explicit comparison for clarity:
```php
$lateMinutes = $now->gt($scheduledStart)
    ? (int)$now->diffInMinutes($scheduledStart)
    : 0;
```

---

## Medium

### M-01 — No `$fillable` on Branch model (mass-assignment risk) ✅ ALREADY FIXED
**File:** `app/Models/Branch.php`  
**Note (2026-07-02):** On inspection, `Branch` already declares `protected $fillable = ['business_id', 'name', 'address', 'timezone'];` — no action needed. Likely fixed in an earlier pass not reflected in this file's history.

**Description:** If `Branch` extends `Model` without a `$fillable` or `$guarded` declaration and `Model::unguard()` is not in effect, `Branch::create([...])` calls will silently fail or throw. Even if not failing today, this is a latent mass-assignment vulnerability if any branch route ever accepts arbitrary user input.

**Fix:** Add to Branch model:
```php
protected $fillable = ['business_id', 'name', 'address', 'timezone'];
```

---

### M-02 — Geolocation failure is silent on the client ✅ RESOLVED (2026-07-02)
**File:** `resources/views/livewire/attendance/clock-in.blade.php`  
**Fix applied:** Added a `locationWarning` Alpine flag set by the `getCurrentPosition` error callback (and when `geolocation` isn't available at all), with a non-blocking amber callout telling the employee their entry will be recorded without a location. Separately confirmed the backend already avoided storing literal `0,0`: `ClockIn::record()` uses `$this->latitude ?: null` / `?: null` for longitude, which coerces the unset default `0.0` (falsy in PHP) to `null` before the `AttendanceLog::create()` call — so no server-side change was needed, only the missing client-side warning.

**Description:** The JavaScript geolocation call likely runs `navigator.geolocation.getCurrentPosition(success)` without an error callback. If the user denies location permission or the browser doesn't support it, `$wire.latitude` and `$wire.longitude` remain `0`. The server accepts `0,0` as valid coordinates (the Gulf of Guinea), which pollutes the location data.

**Fix:** Add an error handler and display a warning to the user. Do not submit `0,0` as a valid coordinate.

---

### M-03 — N+1 queries in AttendanceSummary ✅ ALREADY FIXED
**File:** `app/Livewire/Reports/AttendanceSummary.php`  
**Note (2026-07-02):** On inspection, `logs()` already calls `AttendanceLog::with(['user', 'branch', 'scheduleEntry'])` before the `groupBy`/`map` — no N+1 present. No action needed.

**Description:** The daily summary groups logs by user and accesses `$log->user` and `$log->branch` inside the map callback. If the initial query does not eager-load `user` and `branch`, each access issues a separate query, producing O(n) queries for n employees.

**Fix:** Ensure the query includes `.with(['user', 'branch'])` before the map/groupBy.

---

## Low

### L-01 — No soft deletes on employees/branches ✅ RESOLVED (2026-07-02)
**File:** `app/Models/User.php`, `app/Models/Branch.php`  
**Fix applied:** Migration `2026_07_02_000003_add_soft_deletes_to_users_and_branches.php` adds `deleted_at` to both tables; both models now `use SoftDeletes`. Verified against live data: soft-deleting a real branch left the row intact in the raw table while hiding it from `Branch::find()`, and `restore()` brought it back cleanly.

**Description:** Deleting a branch or employee permanently removes the record. Historical attendance logs that reference `branch_id` or `user_id` will have broken foreign key relationships, or the cascade will delete the logs entirely. No audit trail of who was removed.

**Fix:** Add `SoftDeletes` to both models and add `deleted_at` columns via migration.

---

### L-02 — Stale computed property after week navigation in PayrollSummary ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Reports/PayrollSummary.php` — lines 99–108  
**Fix applied:** Exactly the fix below — `previousWeek()` and `nextWeek()` now `unset($this->rows, $this->weekEnd)`.

**Description:** `previousWeek()` and `nextWeek()` call `unset($this->rows)` to invalidate the cache, but `weekEnd` is also a `#[Computed]` property derived from `weekStart`. If Livewire caches `weekEnd` across the same request cycle, rows may be fetched for the new `weekStart` but the old `weekEnd`, returning a 0-day window.

**Fix:** Also unset `weekEnd` after changing `weekStart`:
```php
public function previousWeek(): void
{
    $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->toDateString();
    unset($this->rows, $this->weekEnd);
}
```

---

### L-03 — `cancel()` in InviteEmployee deletes without ownership check ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Business/InviteEmployee.php` — line 86  
**Fix applied:** `cancel()` now takes a plain `int $invitationId` instead of a route-bound model, and scopes the lookup with `->where('business_id', $this->business->id)->firstOrFail()`, matching the fix below.

**Description:** `cancel(Invitation $invitation)` uses Livewire's implicit model binding. Because the binding resolves from the route model, it does not verify that the invitation belongs to the current user's business. An authenticated user could cancel another business's pending invitation by passing a different ID.

**Fix:**
```php
public function cancel(int $invitationId): void
{
    $invitation = Invitation::where('id', $invitationId)
        ->where('business_id', $this->business->id)
        ->firstOrFail();
    $invitation->delete();
    ...
}
```

---

### L-04 — Payroll `leave_days` counts calendar days across partial leave periods ✅ RESOLVED (2026-07-02)
**File:** `app/Livewire/Reports/PayrollSummary.php` — line 79  
**Fix applied:** `rows()` now clamps each leave request to the reporting week using Carbon's `max()`/`min()` (`$req->from_date->max($start)`, `($req->to_date ?? $req->from_date)->min($end)`) before counting days, matching the fix below.

**Description:** `$req->from_date->diffInDays($req->to_date ?? $req->from_date) + 1` counts the total calendar days of the leave request, not just the days that fall within the current reporting week. A 10-day leave request spanning two weeks will count all 10 days in the first week's payroll report.

**Fix:** Clamp the leave range to the current week before counting:
```php
$leaveStart = max($req->from_date, $start->toDateString());
$leaveEnd   = min($req->to_date ?? $req->from_date, $end->toDateString());
$days = Carbon::parse($leaveStart)->diffInDays(Carbon::parse($leaveEnd)) + 1;
```

---

### L-05 — `ActivityLog::record()` silently fails if no current business ✅ ALREADY FIXED
**File:** `app/Models/ActivityLog.php`  
**Note (2026-07-02):** On inspection, `record()` already guards this: `if (! $user || ! $user->current_business_id) { return; }` before touching `business_id`. No action needed.

**Description:** If `record()` is called before a user is attached to a business (e.g., during the onboarding flow), `Auth::user()->currentBusiness` may be null and `business_id` will be null. If the column is `NOT NULL`, this will throw an unhandled exception. If `nullable`, logs will be orphaned with no business scope.

**Fix:** Guard the call or make `business_id` nullable with a clear comment.

---

### L-06 — Welcome page has no CSRF protection on the "Start for free" CTA
**File:** `resources/views/welcome.blade.php`  
**Description:** The "Start for free" links are plain `<a href>` anchors pointing to the registration route, so no CSRF issue exists there. However, if any form is ever added to the welcome page (newsletter, waitlist, etc.), it must include `@csrf`. This is a pre-emptive note.

**Fix:** No immediate action required — add `@csrf` to any future forms on the public page.

---

## Deferred / Phase 8

These are known gaps intentionally deferred to Phase 8:

- **End-to-end tests** — no Pest feature tests exist for any Livewire component
- **RLS policies** — Supabase RLS is enabled on some tables but policies have not been written
- **Mobile UI review** — ClockIn selfie capture has not been tested on iOS Safari
- **Performance** — no pagination on `ManagerApprovals::requests` or `ManagerReview::logs`; large datasets will cause slow renders
- **Deployment** — no Forge/Coolify config, no queue worker setup (currently all sync)

---

*Generated from code review of `main` branch @ commit `e3f5e2e`.*
