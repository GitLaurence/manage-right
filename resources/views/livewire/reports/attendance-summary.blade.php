    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('Attendance Summary') }}</flux:heading>
                <flux:text>{{ __('Daily attendance overview for') }} <strong>{{ $this->business->name }}</strong></flux:text>
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
                <flux:input wire:model.live="date" type="date" class="w-44" />
            </div>
        </div>

        {{-- Summary Cards --}}
        @php $s = $this->summary; @endphp
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <flux:card class="text-center">
                <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ $s['total_in'] }}</flux:heading>
                <flux:text class="text-xs">{{ __('Clocked In') }}</flux:text>
            </flux:card>
            <flux:card class="text-center">
                <flux:heading size="xl" class="text-amber-600 dark:text-amber-400">{{ $s['late_count'] }}</flux:heading>
                <flux:text class="text-xs">{{ __('Late') }}</flux:text>
            </flux:card>
            <flux:card class="text-center">
                <flux:heading size="xl" class="text-orange-600 dark:text-orange-400">{{ $s['undertime_count'] }}</flux:heading>
                <flux:text class="text-xs">{{ __('Undertime') }}</flux:text>
            </flux:card>
            <flux:card class="text-center">
                <flux:heading size="xl" class="text-purple-600 dark:text-purple-400">{{ $s['overtime_count'] }}</flux:heading>
                <flux:text class="text-xs">{{ __('Overtime') }}</flux:text>
            </flux:card>
        </div>

        {{-- Employee Table --}}
        @if ($this->employeeRows->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.clock class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No attendance records') }}</flux:heading>
                <flux:text class="mt-1">{{ __('No entries found for this date.') }}</flux:text>
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
                            <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Time In') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Time Out') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Flags') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($this->employeeRows as $row)
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
                                <td class="px-4 py-3">
                                    {{ $row->time_in ? $row->time_in->format('h:i A') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $row->time_out ? $row->time_out->format('h:i A') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @if ($row->late_minutes > 0)
                                            <flux:badge color="amber" size="sm">{{ $row->late_minutes }}m late</flux:badge>
                                        @endif
                                        @if ($row->undertime_minutes > 0)
                                            <flux:badge color="orange" size="sm">{{ $row->undertime_minutes }}m UT</flux:badge>
                                        @endif
                                        @if ($row->overtime_minutes > 0)
                                            <flux:badge color="purple" size="sm">{{ $row->overtime_minutes }}m OT</flux:badge>
                                        @endif
                                        @if (! $row->late_minutes && ! $row->undertime_minutes && ! $row->overtime_minutes)
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($row->status === 'approved')
                                        <flux:badge color="green" size="sm">{{ __('Approved') }}</flux:badge>
                                    @elseif ($row->status === 'flagged')
                                        <flux:badge color="red" size="sm">{{ __('Flagged') }}</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('Pending') }}</flux:badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </flux:card>
        @endif

    </div>
