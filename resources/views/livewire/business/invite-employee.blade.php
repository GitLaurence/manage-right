    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div>
            <flux:heading size="xl">{{ __('Invite Employee') }}</flux:heading>
            <flux:text>{{ __('Send an invitation to join') }} <strong>{{ $this->business->name }}</strong></flux:text>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Invite Form --}}
            <flux:card>
                <form wire:submit="send" class="space-y-5">
                    <flux:heading size="lg">{{ __('New Invitation') }}</flux:heading>

                    <flux:select wire:model="role" :label="__('Role')" required>
                        <flux:select.option value="employee">{{ __('Employee') }}</flux:select.option>
                        <flux:select.option value="manager">{{ __('Manager') }}</flux:select.option>
                    </flux:select>

                    @if ($this->branches->isNotEmpty())
                        <flux:select wire:model="branchId" :label="__('Assign to Branch')">
                            <flux:select.option value="">{{ __('No branch yet') }}</flux:select.option>
                            @foreach ($this->branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <flux:input
                        wire:model="position"
                        :label="__('Position / Job Title')"
                        :placeholder="__('e.g. Barista, Cashier')"
                    />

                    <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Send Invitation') }}</span>
                        <span wire:loading>{{ __('Creating…') }}</span>
                    </flux:button>
                </form>

                @if ($inviteLink)
                    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
                        <flux:text class="mb-2 font-medium text-green-800 dark:text-green-200">
                            {{ __('Invitation created! Share this link:') }}
                        </flux:text>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 truncate rounded bg-white px-2 py-1 text-xs dark:bg-zinc-900">
                                {{ $inviteLink }}
                            </code>
                            <flux:button
                                size="sm"
                                icon="clipboard"
                                x-on:click="navigator.clipboard.writeText('{{ $inviteLink }}')"
                            >
                                {{ __('Copy') }}
                            </flux:button>
                        </div>
                    </div>
                @endif
            </flux:card>

            {{-- Pending Invitations --}}
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Pending Invitations') }}</flux:heading>

                @forelse ($this->pendingInvitations as $invitation)
                    <flux:card class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-medium">{{ $invitation->email ?? __('Awaiting registration') }}</p>
                            <flux:text>
                                {{ ucfirst($invitation->role) }}
                                @if ($invitation->position)
                                    · {{ $invitation->position }}
                                @endif
                                @if ($invitation->branch)
                                    · {{ $invitation->branch->name }}
                                @endif
                            </flux:text>
                            <flux:text class="text-xs">
                                {{ __('Expires') }} {{ $invitation->expires_at->diffForHumans() }}
                            </flux:text>
                        </div>
                        <flux:button
                            size="sm"
                            variant="danger"
                            wire:click="cancel({{ $invitation->id }})"
                            wire:confirm="{{ __('Cancel this invitation?') }}"
                        >
                            {{ __('Cancel') }}
                        </flux:button>
                    </flux:card>
                @empty
                    <flux:text class="text-zinc-400">{{ __('No pending invitations.') }}</flux:text>
                @endforelse
            </div>

        </div>
    </div>
