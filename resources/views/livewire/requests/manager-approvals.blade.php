<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('Request Approvals') }}</flux:heading>
                <flux:text>{{ __('Review employee leave, overtime, and undertime requests.') }}</flux:text>
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

                <flux:select wire:model.live="statusFilter" class="w-32">
                    <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                    <flux:select.option value="approved">{{ __('Approved') }}</flux:select.option>
                    <flux:select.option value="rejected">{{ __('Rejected') }}</flux:select.option>
                    <flux:select.option value="all">{{ __('All') }}</flux:select.option>
                </flux:select>
            </div>
        </div>

        @if ($this->requests->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.inbox class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No requests found') }}</flux:heading>
                <flux:text class="mt-1">{{ __('No requests match the current filters.') }}</flux:text>
            </flux:card>
        @else
            <div class="space-y-3">
                @foreach ($this->requests as $req)
                    <flux:card wire:key="{{ $req->id }}" class="flex flex-wrap items-start gap-4">

                        <div class="flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:avatar size="sm" :name="$req->user->name" :initials="$req->user->initials()" />
                                <span class="font-medium">{{ $req->user->name }}</span>

                                <flux:badge color="{{ match($req->type) { 'leave' => 'blue', 'overtime' => 'purple', 'undertime' => 'orange', default => 'zinc' } }}">
                                    {{ $req->typeLabel() }}
                                    @if ($req->leave_type)
                                        — {{ $req->leaveTypeLabel() }}
                                    @endif
                                </flux:badge>

                                @if ($req->status === 'approved')
                                    <flux:badge color="green" icon="check">{{ __('Approved') }}</flux:badge>
                                @elseif ($req->status === 'rejected')
                                    <flux:badge color="red" icon="x-mark">{{ __('Rejected') }}</flux:badge>
                                @else
                                    <flux:badge color="amber" icon="clock">{{ __('Pending') }}</flux:badge>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-3 text-sm">
                                <span class="flex items-center gap-1 text-zinc-500">
                                    <flux:icon.calendar class="size-4" />
                                    @if ($req->type === 'leave')
                                        {{ $req->from_date->format('M d, Y') }}
                                        @if ($req->to_date && ! $req->from_date->eq($req->to_date))
                                            — {{ $req->to_date->format('M d, Y') }}
                                        @endif
                                    @else
                                        {{ $req->from_date->format('M d, Y') }} · {{ $req->hours }}h
                                    @endif
                                </span>

                                @if ($req->branch)
                                    <span class="text-zinc-400">{{ $req->branch->name }}</span>
                                @endif
                            </div>

                            <flux:text class="text-sm text-zinc-500">{{ $req->reason }}</flux:text>

                            @if ($req->manager_remarks)
                                <flux:text class="text-xs text-zinc-400">
                                    <strong>{{ __('Remarks:') }}</strong> {{ $req->manager_remarks }}
                                </flux:text>
                            @endif

                            @if ($req->reviewer && $req->reviewed_at)
                                <flux:text class="text-xs text-zinc-400">
                                    {{ __('Reviewed by') }} {{ $req->reviewer->name }} · {{ $req->reviewed_at->diffForHumans() }}
                                </flux:text>
                            @endif
                        </div>

                        @if ($req->isPending())
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $req->id }})">
                                    {{ __('Approve') }}
                                </flux:button>
                                <flux:button size="sm" variant="danger" icon="x-mark" wire:click="openReject({{ $req->id }})">
                                    {{ __('Reject') }}
                                </flux:button>
                            </div>
                        @endif

                    </flux:card>
                @endforeach
            </div>
        @endif

    </div>

    <flux:modal name="reject-modal" class="max-w-sm">
        <form wire:submit="reject" class="space-y-5">
            <flux:heading>{{ __('Reject Request') }}</flux:heading>
            <flux:text>{{ __('Optionally add a remark explaining the rejection.') }}</flux:text>

            <flux:textarea
                wire:model="remarks"
                :label="__('Remarks')"
                :placeholder="__('e.g. Insufficient leave credits')"
                rows="3"
            />

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Reject Request') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
