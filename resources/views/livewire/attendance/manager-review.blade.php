<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('Attendance Review') }}</flux:heading>
                <flux:text>{{ __('Review employee clock-ins for') }} <strong>{{ $this->business->name }}</strong></flux:text>
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

        @if ($this->logs->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.clock class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No attendance records') }}</flux:heading>
                <flux:text class="mt-1">{{ __('No entries found for this date and branch.') }}</flux:text>
            </flux:card>
        @else
            <div class="space-y-3">
                @foreach ($this->logs as $log)
                    <flux:card wire:key="{{ $log->id }}" class="flex flex-wrap items-start gap-4">

                        {{-- Selfie thumbnail --}}
                        <div class="size-16 flex-shrink-0 overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                            @if ($log->selfie_url)
                                <img src="{{ $log->selfie_url }}" alt="Selfie" class="h-full w-full object-cover" loading="lazy" />
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <flux:icon.user class="size-8 text-zinc-400" />
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:avatar size="sm" :name="$log->user->name" :initials="$log->user->initials()" />
                                <span class="font-medium">{{ $log->user->name }}</span>
                                <flux:badge :color="$log->type === 'time_in' ? 'green' : 'blue'">
                                    {{ $log->type === 'time_in' ? __('Time In') : __('Time Out') }}
                                </flux:badge>
                                @if ($log->status === 'approved')
                                    <flux:badge color="green" icon="check">{{ __('Approved') }}</flux:badge>
                                @elseif ($log->status === 'flagged')
                                    <flux:badge color="red" icon="flag">{{ __('Flagged') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc">{{ __('Pending') }}</flux:badge>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-3 text-sm">
                                <span class="flex items-center gap-1">
                                    <flux:icon.clock class="size-4 text-zinc-400" />
                                    {{ $log->logged_at->format('h:i A') }}
                                </span>

                                @if ($log->isLate())
                                    <flux:badge color="amber" size="sm">{{ $log->late_minutes }}m {{ __('late') }}</flux:badge>
                                @endif

                                @if ($log->hasUndertime())
                                    <flux:badge color="orange" size="sm">{{ $log->undertime_minutes }}m {{ __('undertime') }}</flux:badge>
                                @endif

                                @if ($log->hasOvertime())
                                    <flux:badge color="purple" size="sm">{{ $log->overtime_minutes }}m {{ __('overtime') }}</flux:badge>
                                @endif

                                @if ($log->latitude)
                                    <a
                                        href="https://maps.google.com/?q={{ $log->latitude }},{{ $log->longitude }}"
                                        target="_blank"
                                        class="flex items-center gap-1 text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        <flux:icon.map-pin class="size-4" />
                                        {{ __('View location') }}
                                    </a>
                                @endif
                            </div>

                            @if ($log->manager_note)
                                <flux:text class="text-xs text-red-600 dark:text-red-400">
                                    <strong>{{ __('Note:') }}</strong> {{ $log->manager_note }}
                                </flux:text>
                            @endif

                            @if ($log->branch)
                                <flux:text class="text-xs text-zinc-400">{{ $log->branch->name }}</flux:text>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if ($log->status !== 'approved')
                            <div class="flex gap-2">
                                <flux:button
                                    size="sm"
                                    variant="primary"
                                    icon="check"
                                    wire:click="approve({{ $log->id }})"
                                >
                                    {{ __('Approve') }}
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="flag"
                                    wire:click="openFlag({{ $log->id }})"
                                >
                                    {{ __('Flag') }}
                                </flux:button>
                            </div>
                        @endif

                    </flux:card>
                @endforeach
            </div>
        @endif

    </div>

    <flux:modal name="flag-modal" class="max-w-sm">
        <form wire:submit="flag" class="space-y-5">
            <flux:heading>{{ __('Flag Entry') }}</flux:heading>
            <flux:text>{{ __('Optionally add a note explaining why this entry is flagged.') }}</flux:text>

            <flux:textarea
                wire:model="managerNote"
                :label="__('Manager Note')"
                :placeholder="__('e.g. Selfie does not match employee appearance')"
                rows="3"
            />

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Flag Entry') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
