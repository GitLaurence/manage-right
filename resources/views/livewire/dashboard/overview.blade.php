<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:text>{{ __('Overview for') }} <strong>{{ $this->business->name }}</strong> · {{ today()->format('l, F j, Y') }}</flux:text>
        </div>

        {{-- Today's Stats --}}
        @php $s = $this->todayStats; @endphp
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <flux:card class="flex flex-col gap-1">
                <flux:text class="text-xs font-medium text-zinc-500">{{ __("Today's Present") }}</flux:text>
                <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ $s['present'] }}</flux:heading>
            </flux:card>
            <flux:card class="flex flex-col gap-1">
                <flux:text class="text-xs font-medium text-zinc-500">{{ __('Late Today') }}</flux:text>
                <flux:heading size="xl" class="text-amber-600 dark:text-amber-400">{{ $s['late'] }}</flux:heading>
            </flux:card>
            <flux:card class="flex flex-col gap-1">
                <flux:text class="text-xs font-medium text-zinc-500">{{ __('Pending Reviews') }}</flux:text>
                <flux:heading size="xl" class="text-zinc-700 dark:text-zinc-300">{{ $s['pending'] }}</flux:heading>
            </flux:card>
            <flux:card class="flex flex-col gap-1">
                <flux:text class="text-xs font-medium text-zinc-500">{{ __('Pending Requests') }}</flux:text>
                <flux:heading size="xl" class="text-blue-600 dark:text-blue-400">{{ $this->pendingRequests }}</flux:heading>
            </flux:card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Branch Snapshots --}}
            <div class="flex flex-col gap-3">
                <flux:heading>{{ __('Branch Snapshot') }}</flux:heading>

                @if ($this->branchSnapshots->isEmpty())
                    <flux:card class="py-8 text-center">
                        <flux:text class="text-zinc-400">{{ __('No branches found.') }}</flux:text>
                    </flux:card>
                @else
                    @foreach ($this->branchSnapshots as $snap)
                        <flux:card wire:key="{{ $snap->branch->id }}" class="flex items-center justify-between gap-4">
                            <div>
                                <span class="font-medium">{{ $snap->branch->name }}</span>
                                <flux:text class="text-sm text-zinc-500">
                                    {{ $snap->employee_count }} {{ __('employees') }}
                                </flux:text>
                            </div>
                            <div class="flex gap-3 text-sm">
                                <div class="text-center">
                                    <div class="font-semibold text-green-600 dark:text-green-400">{{ $snap->present }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('In') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-amber-600 dark:text-amber-400">{{ $snap->late }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('Late') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-zinc-500">{{ $snap->employee_count - $snap->present }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('Absent') }}</div>
                                </div>
                            </div>
                        </flux:card>
                    @endforeach
                @endif
            </div>

            {{-- Recent Activity --}}
            <div class="flex flex-col gap-3">
                <flux:heading>{{ __('Recent Activity') }}</flux:heading>

                @if ($this->recentActivity->isEmpty())
                    <flux:card class="py-8 text-center">
                        <flux:text class="text-zinc-400">{{ __('No activity recorded yet.') }}</flux:text>
                    </flux:card>
                @else
                    <flux:card class="divide-y divide-zinc-100 p-0 dark:divide-zinc-800">
                        @foreach ($this->recentActivity as $log)
                            <div wire:key="{{ $log->id }}" class="flex items-start gap-3 px-4 py-3">
                                <flux:avatar size="sm" :name="$log->user->name" :initials="$log->user->initials()" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm">
                                        <span class="font-medium">{{ $log->user->name }}</span>
                                        <span class="text-zinc-500"> {{ $log->action }}</span>
                                        @if ($log->branch)
                                            <span class="text-zinc-400"> · {{ $log->branch->name }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </flux:card>
                @endif
            </div>

        </div>
    </div>
</x-layouts::app>
