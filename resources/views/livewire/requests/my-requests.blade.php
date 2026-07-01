<x-layouts::app :title="__('My Requests')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('My Requests') }}</flux:heading>
                <flux:text>{{ __('Submit and track your leave, overtime, and undertime requests.') }}</flux:text>
            </div>
            @if (! $showForm)
                <flux:button variant="primary" icon="plus" wire:click="openForm">
                    {{ __('New Request') }}
                </flux:button>
            @endif
        </div>

        {{-- Submit Form --}}
        @if ($showForm)
            <flux:card class="space-y-5">
                <flux:heading>{{ __('New Request') }}</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:select wire:model.live="type" :label="__('Request Type')">
                        <flux:select.option value="leave">{{ __('Leave') }}</flux:select.option>
                        <flux:select.option value="overtime">{{ __('Overtime') }}</flux:select.option>
                        <flux:select.option value="undertime">{{ __('Undertime') }}</flux:select.option>
                    </flux:select>

                    @if ($type === 'leave')
                        <flux:select wire:model="leaveType" :label="__('Leave Type')">
                            <flux:select.option value="vacation">{{ __('Vacation Leave') }}</flux:select.option>
                            <flux:select.option value="sick">{{ __('Sick Leave') }}</flux:select.option>
                            <flux:select.option value="emergency">{{ __('Emergency Leave') }}</flux:select.option>
                            <flux:select.option value="unpaid">{{ __('Unpaid Leave') }}</flux:select.option>
                        </flux:select>
                    @else
                        <flux:input wire:model="hours" type="number" step="0.5" min="0.5" max="24" :label="__('Hours')" :placeholder="__('e.g. 2')" />
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="fromDate" type="date" :label="$type === 'leave' ? __('From Date') : __('Date')" />
                    @if ($type === 'leave')
                        <flux:input wire:model="toDate" type="date" :label="__('To Date')" />
                    @endif
                </div>

                <flux:textarea wire:model="reason" :label="__('Reason')" :placeholder="__('Describe the reason for your request...')" rows="3" />

                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancel">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" wire:click="submit">{{ __('Submit Request') }}</flux:button>
                </div>
            </flux:card>
        @endif

        {{-- Request History --}}
        @if ($this->requests->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.inbox class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No requests yet') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Submit a leave, overtime, or undertime request to get started.') }}</flux:text>
            </flux:card>
        @else
            <div class="space-y-3">
                @foreach ($this->requests as $req)
                    <flux:card wire:key="{{ $req->id }}" class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-medium">
                                    {{ $req->typeLabel() }}
                                    @if ($req->leave_type)
                                        — {{ $req->leaveTypeLabel() }}
                                    @endif
                                </span>

                                @if ($req->status === 'approved')
                                    <flux:badge color="green" icon="check">{{ __('Approved') }}</flux:badge>
                                @elseif ($req->status === 'rejected')
                                    <flux:badge color="red" icon="x-mark">{{ __('Rejected') }}</flux:badge>
                                @else
                                    <flux:badge color="amber" icon="clock">{{ __('Pending') }}</flux:badge>
                                @endif
                            </div>

                            <flux:text class="text-sm">
                                @if ($req->type === 'leave')
                                    {{ $req->from_date->format('M d, Y') }}
                                    @if ($req->to_date && ! $req->from_date->eq($req->to_date))
                                        — {{ $req->to_date->format('M d, Y') }}
                                    @endif
                                @else
                                    {{ $req->from_date->format('M d, Y') }} · {{ $req->hours }}h
                                @endif
                            </flux:text>

                            <flux:text class="text-sm text-zinc-500">{{ $req->reason }}</flux:text>

                            @if ($req->manager_remarks)
                                <flux:text class="text-xs text-zinc-500">
                                    <strong>{{ __('Remarks:') }}</strong> {{ $req->manager_remarks }}
                                </flux:text>
                            @endif
                        </div>

                        <flux:text class="text-xs text-zinc-400">{{ $req->created_at->diffForHumans() }}</flux:text>
                    </flux:card>
                @endforeach
            </div>
        @endif

    </div>
</x-layouts::app>
