    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Branches') }}</flux:heading>
                <flux:text>{{ __('Manage locations for') }} <strong>{{ $this->business->name }}</strong></flux:text>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="openCreate">
                {{ __('Add Branch') }}
            </flux:button>
        </div>

        @if ($this->branches->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.layout-grid class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No branches yet') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Add your first branch to start managing employees.') }}</flux:text>
                <flux:button variant="primary" class="mt-6" wire:click="openCreate">
                    {{ __('Add Branch') }}
                </flux:button>
            </flux:card>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Branch') }}</flux:table.column>
                    <flux:table.column>{{ __('Address') }}</flux:table.column>
                    <flux:table.column>{{ __('Timezone') }}</flux:table.column>
                    <flux:table.column class="text-end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->branches as $branch)
                        <flux:table.row wire:key="{{ $branch->id }}">
                            <flux:table.cell class="font-medium">{{ $branch->name }}</flux:table.cell>
                            <flux:table.cell>{{ $branch->address ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $branch->timezone }}</flux:table.cell>
                            <flux:table.cell class="text-end">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" icon="pencil" wire:click="openEdit({{ $branch->id }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        wire:click="delete({{ $branch->id }})"
                                        wire:confirm="{{ __('Delete this branch? This cannot be undone.') }}"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif

    </div>

    <flux:modal name="branch-form" class="max-w-md">
        <form wire:submit="save" class="space-y-6">
            <flux:heading>
                {{ $editingId ? __('Edit Branch') : __('Add Branch') }}
            </flux:heading>

            <flux:input
                wire:model="branchName"
                :label="__('Branch Name')"
                :placeholder="__('e.g. Main Branch')"
                required
                autofocus
            />

            <flux:input
                wire:model="address"
                :label="__('Address')"
                :placeholder="__('Street, City')"
            />

            <flux:select wire:model="timezone" :label="__('Timezone')" required>
                <flux:select.option value="Asia/Manila">Asia/Manila (PHT)</flux:select.option>
                <flux:select.option value="Asia/Singapore">Asia/Singapore (SGT)</flux:select.option>
                <flux:select.option value="Asia/Tokyo">Asia/Tokyo (JST)</flux:select.option>
                <flux:select.option value="UTC">UTC</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ $editingId ? __('Update') : __('Add Branch') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
