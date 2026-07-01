<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => 'Employee Management Made Simple'])
</head>
<body class="bg-white text-zinc-900 antialiased">

{{-- ─────────────────────────────────────────────────────────
     NAV
────────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-50 border-b border-zinc-100 bg-white/90 backdrop-blur-sm">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-900">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <span class="text-sm font-semibold tracking-tight text-zinc-900">Manage Right</span>
        </a>

        <nav class="flex items-center gap-1">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-zinc-900 px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-zinc-700">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:text-zinc-900">
                    Log in
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-1 rounded-lg bg-zinc-900 px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-zinc-700">
                        Get started
                    </a>
                @endif
            @endauth
        </nav>
    </div>
</header>

<main>

{{-- ─────────────────────────────────────────────────────────
     HERO
────────────────────────────────────────────────────────── --}}
<section class="border-b border-zinc-100 px-6 pb-0 pt-24">
    <div class="mx-auto max-w-3xl">

        {{-- Badge --}}
        <div class="mb-7 flex justify-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-zinc-50 px-3.5 py-1 text-xs font-medium tracking-wide text-zinc-500">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Built for cafés, restaurants, salons &amp; retail
            </span>
        </div>

        {{-- Headline --}}
        <h1 class="text-center text-[3.25rem] font-bold leading-[1.1] tracking-[-0.04em] text-zinc-900 sm:text-[4.5rem]">
            Your whole team,<br />one place.
        </h1>

        {{-- Subtext --}}
        <p class="mx-auto mt-5 max-w-md text-center text-base leading-relaxed text-zinc-500">
            Selfie time-in, smart scheduling, leave requests, and payroll summaries — everything a small business needs, nothing it doesn't.
        </p>

        {{-- CTAs --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-zinc-700">
                    Go to dashboard
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-zinc-700">
                    Start for free
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-zinc-200 px-6 py-3 text-sm font-semibold text-zinc-600 transition-colors hover:border-zinc-300 hover:text-zinc-900">
                    Log in
                </a>
            @endauth
        </div>

        {{-- App preview --}}
        <div class="relative mt-16">
            {{-- Ambient shadow below the window --}}
            <div class="absolute -bottom-6 left-8 right-8 h-12 rounded-full bg-zinc-900/10 blur-2xl"></div>

            {{-- Browser chrome --}}
            <div class="relative overflow-hidden rounded-t-2xl border border-b-0 border-zinc-200 bg-white shadow-xl shadow-zinc-900/[.07]">

                {{-- Traffic lights + URL bar --}}
                <div class="flex items-center gap-3 border-b border-zinc-100 bg-zinc-50 px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <div class="h-3 w-3 rounded-full bg-zinc-300"></div>
                        <div class="h-3 w-3 rounded-full bg-zinc-300"></div>
                        <div class="h-3 w-3 rounded-full bg-zinc-300"></div>
                    </div>
                    <div class="flex flex-1 items-center justify-center rounded-md border border-zinc-200 bg-white px-3 py-1">
                        <span class="text-[11px] text-zinc-400">manage-right.app/dashboard</span>
                    </div>
                    <div class="w-16"></div>
                </div>

                {{-- Dashboard UI --}}
                <div class="flex min-h-[420px] divide-x divide-zinc-100">

                    {{-- Sidebar --}}
                    <aside class="hidden w-52 flex-shrink-0 bg-zinc-50 p-4 sm:block">
                        <div class="mb-5 flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-zinc-900">
                                <div class="h-3 w-3 rounded-sm bg-white/70"></div>
                            </div>
                            <div class="h-2.5 w-24 rounded-full bg-zinc-200"></div>
                        </div>
                        <p class="mb-2 px-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Platform</p>
                        @foreach([
                            ['Dashboard', true],
                            ['Attendance', false],
                            ['Scheduling', false],
                            ['Requests', false],
                            ['Reports', false],
                        ] as [$item, $active])
                        <div class="mb-0.5 flex items-center gap-2.5 rounded-lg px-2 py-1.5 {{ $active ? 'bg-white shadow-sm border border-zinc-100' : '' }}">
                            <div class="h-3.5 w-3.5 rounded {{ $active ? 'bg-zinc-900' : 'bg-zinc-300' }}"></div>
                            <span class="text-xs {{ $active ? 'font-semibold text-zinc-900' : 'text-zinc-400' }}">{{ $item }}</span>
                        </div>
                        @endforeach
                    </aside>

                    {{-- Main panel --}}
                    <div class="flex-1 p-5">

                        {{-- Page header --}}
                        <div class="mb-5 flex items-start justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-zinc-900">Dashboard</h2>
                                <p class="text-xs text-zinc-400">Brew Brothers Café · Tuesday, Jul 1, 2026</p>
                            </div>
                            <div class="rounded-lg border border-zinc-200 px-2.5 py-1 text-xs text-zinc-500">+ Invite employee</div>
                        </div>

                        {{-- Stat cards --}}
                        <div class="mb-5 grid grid-cols-4 gap-3">
                            @foreach([
                                ['18', 'Clocked in',  'text-emerald-600', 'bg-emerald-50', 'border-emerald-100'],
                                ['3',  'Late today',  'text-amber-600',   'bg-amber-50',   'border-amber-100'],
                                ['2',  'Pending',     'text-zinc-600',    'bg-zinc-50',    'border-zinc-100'],
                                ['5',  'Requests',    'text-violet-600',  'bg-violet-50',  'border-violet-100'],
                            ] as [$n, $l, $nc, $bg, $bc])
                            <div class="rounded-xl border {{ $bc }} {{ $bg }} p-3">
                                <div class="text-xl font-bold {{ $nc }}">{{ $n }}</div>
                                <div class="mt-0.5 text-[10px] font-medium text-zinc-500">{{ $l }}</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Table --}}
                        <div class="overflow-hidden rounded-xl border border-zinc-100">
                            <div class="border-b border-zinc-100 bg-zinc-50 px-4 py-2.5">
                                <span class="text-xs font-semibold text-zinc-600">Today's Attendance</span>
                            </div>
                            @foreach([
                                ['Maria Reyes',    'Cashier',    '08:01 AM', 'On time',  'text-emerald-600 bg-emerald-50'],
                                ['Juan Dela Cruz', 'Barista',    '08:18 AM', '18m late', 'text-amber-600 bg-amber-50'],
                                ['Ana Santos',     'Supervisor', '07:55 AM', 'On time',  'text-emerald-600 bg-emerald-50'],
                                ['Rico Cruz',      'Barista',    '—',        'Not in',   'text-zinc-400 bg-zinc-50'],
                            ] as [$name, $role, $time, $status, $badge])
                            <div class="flex items-center justify-between border-b border-zinc-50 px-4 py-2.5 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-zinc-100 text-[9px] font-bold text-zinc-500">
                                        {{ strtoupper(substr($name, 0, 1).substr(explode(' ', $name)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-zinc-800">{{ $name }}</div>
                                        <div class="text-[10px] text-zinc-400">{{ $role }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-zinc-500">{{ $time }}</span>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $badge }}">{{ $status }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ─────────────────────────────────────────────────────────
     TRUSTED BY STRIP
────────────────────────────────────────────────────────── --}}
<section class="border-b border-zinc-100 bg-zinc-50 py-5">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-8 gap-y-2 px-6">
        <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-zinc-400">Perfect for</span>
        @foreach(['Cafés', 'Restaurants', 'Salons', 'Retail Stores', 'Clinics', 'Bakeries', 'Gyms', 'Kiosks', 'Spas'] as $type)
            <span class="text-sm text-zinc-400">{{ $type }}</span>
        @endforeach
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────
     FEATURES
────────────────────────────────────────────────────────── --}}
<section class="border-b border-zinc-100 px-6 py-28">
    <div class="mx-auto max-w-6xl">

        <div class="mb-14 max-w-sm">
            <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-zinc-400">Features</p>
            <h2 class="text-3xl font-bold leading-tight tracking-tight text-zinc-900">
                Everything you need to manage your team.
            </h2>
        </div>

        <div class="grid gap-px border border-zinc-100 bg-zinc-100 sm:grid-cols-2 lg:grid-cols-3" style="border-radius: 16px; overflow: hidden;">
            @php
            $features = [
                [
                    'title' => 'Selfie Time-In / Out',
                    'desc'  => 'Employees clock in and out with a selfie. Auto-detects late, undertime, and overtime based on their schedule.',
                    'path'  => 'M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z',
                ],
                [
                    'title' => 'Smart Scheduling',
                    'desc'  => 'Build shift templates once. Drag, assign, and publish weekly schedules per branch in minutes.',
                    'path'  => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
                ],
                [
                    'title' => 'Payroll Reports',
                    'desc'  => 'Weekly per-employee breakdown of hours, late, undertime, and overtime. Export to CSV for payroll.',
                    'path'  => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
                ],
                [
                    'title' => 'Leave & OT Requests',
                    'desc'  => 'Employees submit leave, overtime, and undertime requests from their phone. Managers approve with remarks.',
                    'path'  => 'M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z',
                ],
                [
                    'title' => 'Multi-Branch Dashboard',
                    'desc'  => 'Monitor every branch from one owner-level view. Headcounts, late counts, and pending reviews at a glance.',
                    'path'  => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
                ],
                [
                    'title' => 'Attendance Review',
                    'desc'  => 'Managers approve or flag every selfie entry. Add notes, review geolocation, and keep a full audit trail.',
                    'path'  => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
                ],
            ];
            @endphp

            @foreach($features as $f)
            <div class="bg-white p-8">
                <div class="mb-5 flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50">
                    <svg class="h-4.5 w-4.5 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['path'] }}" />
                    </svg>
                </div>
                <h3 class="mb-2 text-sm font-semibold text-zinc-900">{{ $f['title'] }}</h3>
                <p class="text-sm leading-relaxed text-zinc-500">{{ $f['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────
     HOW IT WORKS
────────────────────────────────────────────────────────── --}}
<section class="border-b border-zinc-100 px-6 py-28">
    <div class="mx-auto max-w-6xl">

        <div class="mb-14 flex flex-col items-center text-center">
            <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-zinc-400">How it works</p>
            <h2 class="max-w-xs text-3xl font-bold leading-tight tracking-tight text-zinc-900">
                Up and running in minutes.
            </h2>
        </div>

        <div class="grid gap-12 sm:grid-cols-3">
            @foreach([
                ['01', 'Create your business', 'Register, add branches, and invite your team by email. No setup fee, no hardware.'],
                ['02', 'Set up your schedule',  'Build shift templates, assign employees to each day and branch, then publish.'],
                ['03', 'Track and report',      'Employees clock in with selfies. You review, approve, and export payroll data weekly.'],
            ] as [$n, $title, $desc])
            <div>
                <div class="mb-4 text-[11px] font-bold tabular-nums tracking-widest text-zinc-300">{{ $n }}</div>
                <h3 class="mb-2 text-base font-semibold tracking-tight text-zinc-900">{{ $title }}</h3>
                <p class="text-sm leading-relaxed text-zinc-500">{{ $desc }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ─────────────────────────────────────────────────────────
     CTA
────────────────────────────────────────────────────────── --}}
<section class="px-6 py-28">
    <div class="mx-auto max-w-6xl">

        <div class="rounded-2xl bg-zinc-900 px-10 py-16 text-center sm:py-20">
            <h2 class="mx-auto max-w-xl text-3xl font-bold leading-tight tracking-[-0.03em] text-white sm:text-4xl">
                Ready to ditch the group chats and paper logs?
            </h2>
            <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-zinc-400">
                Set up your business in minutes. No technical knowledge required.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                        Go to dashboard
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                        Create a free account
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold text-zinc-400 transition-colors hover:text-white">
                        Log in
                    </a>
                @endauth
            </div>
        </div>

    </div>
</section>

</main>

{{-- ─────────────────────────────────────────────────────────
     FOOTER
────────────────────────────────────────────────────────── --}}
<footer class="border-t border-zinc-100 px-6 py-6">
    <div class="mx-auto flex max-w-6xl items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="flex h-5 w-5 items-center justify-center rounded-md bg-zinc-900">
                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <span class="text-xs font-semibold text-zinc-900">Manage Right</span>
        </div>
        <p class="text-xs text-zinc-400">&copy; {{ date('Y') }} Manage Right. All rights reserved.</p>
    </div>
</footer>

@fluxScripts
</body>
</html>
