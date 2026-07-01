<x-layouts::app :title="__('Payroll Summary')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('Payroll Summary') }}</flux:heading>
                <flux:text>{{ __('Weekly attendance and hours summary for payroll processing.') }}</flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($this->branches->count() > 1)
                    <flux:select wire:model.live="branchId" class="w-36">
                        <flux:select.option value="">{{ __('All branches') }}</flux:select.option>
                        @foreach ($this->branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:button size="sm" icon="arrow-down-tray" wire:click="exportCsv">
                    {{ __('Export CSV') }}
                </flux:button>
            </div>
        </div>

        {{-- Week Navigator --}}
        <div class="flex items-center gap-3">
            <flux:button size="sm" icon="chevron-left" wire:click="previousWeek" />
            <flux:heading class="min-w-56 text-center">
                {{ \Carbon\Carbon::parse($weekStart)->format('M d') }}
                — {{ \Carbon\Carbon::parse($this->weekEnd)->format('M d, Y') }}
            </flux:heading>
            <flux:button size="sm" icon="chevron-right" wire:click="nextWeek" />
        </div>

        @if ($this->rows->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.chart-bar class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No data for this week') }}</flux:heading>
                <flux:text class="mt-1">{{ __('No attendance records found for the selected period.') }}</flux:text>
            </flux:card>
        @else
            <flux:card class="overflow-x-auto p-0">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Employee') }}</th>
                            @if (! $branchId)
                                <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Branch') }}</th>
                            @endif
                            <th class="px-4 py-3 text-center font-medium text-zinc-500">{{ __('Days Present') }}</th>
                            <th class="px-4 py-3 text-center font-medium text-zinc-500">{{ __('Leave Days') }}</th>
                            <th class="px-4 py-3 text-center font-medium text-zinc-500">{{ __('Late (hrs)') }}</th>
                            <th class="px-4 py-3 text-center font-medium text-zinc-500">{{ __('Undertime (hrs)') }}</th>
                            <th class="px-4 py-3 text-center font-medium text-zinc-500">{{ __('Overtime (hrs)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($this->rows as $row)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:avatar size="sm" :name="$row->user->name" :initials="$row->user->initials()" />
                                        <span class="font-medium">{{ $row->user->name }}</span>
                                    </div>
                                </td>
                                @if (! $branchId)
                                    <td class="px-4 py-3 text-zinc-500">{{ $row->branch?->name ?? '—' }}</td>
                                @endif
                                <td class="px-4 py-3 text-center">
                                    <flux:badge color="green">{{ $row->days_present }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($row->leave_days > 0)
                                        <flux:badge color="blue">{{ $row->leave_days }}</flux:badge>
                                    @else
                                        <span class="text-zinc-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($row->late_hours > 0)
                                        <flux:badge color="amber">{{ $row->late_hours }}</flux:badge>
                                    @else
                                        <span class="text-zinc-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($row->undertime_hours > 0)
                                        <flux:badge color="orange">{{ $row->undertime_hours }}</flux:badge>
                                    @else
                                        <span class="text-zinc-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($row->overtime_hours > 0)
                                        <flux:badge color="purple">{{ $row->overtime_hours }}</flux:badge>
                                    @else
                                        <span class="text-zinc-400">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </flux:card>
        @endif

    </div>
</x-layouts::app>
