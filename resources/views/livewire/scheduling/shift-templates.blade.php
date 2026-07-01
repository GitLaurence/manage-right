<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Shift Templates') }}</flux:heading>
                <flux:text>{{ __('Reusable shifts for') }} <strong>{{ $this->business->name }}</strong></flux:text>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="openCreate">
                {{ __('New Template') }}
            </flux:button>
        </div>

        @if ($this->templates->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.layout-grid class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No shift templates yet') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Create shift templates to use when building schedules.') }}</flux:text>
                <flux:button variant="primary" class="mt-6" wire:click="openCreate">
                    {{ __('Create First Template') }}
                </flux:button>
            </flux:card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->templates as $template)
                    @php $c = $template->colorClasses(); @endphp
                    <flux:card wire:key="{{ $template->id }}" class="flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-block size-3 rounded-full {{ str_replace(['bg-', 'dark:bg-'], ['bg-', 'dark:bg-'], $c['bg']) }}"></span>
                                <flux:heading size="sm">{{ $template->name }}</flux:heading>
                            </div>
                            <span class="rounded-full border px-2 py-0.5 text-xs font-medium {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }}">
                                {{ $template->color }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <flux:text class="flex items-center gap-2">
                                <flux:icon.clock class="size-4" />
                                {{ $template->formattedHours() }}
                            </flux:text>
                            @if ($template->break_minutes > 0)
                                <flux:text class="flex items-center gap-2 text-xs">
                                    <flux:icon.pause-circle class="size-4" />
                                    {{ $template->break_minutes }} {{ __('min break') }}
                                </flux:text>
                            @endif
                        </div>

                        <div class="flex gap-2 pt-1">
                            <flux:button size="sm" icon="pencil" wire:click="openEdit({{ $template->id }})">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="danger"
                                icon="trash"
                                wire:click="delete({{ $template->id }})"
                                wire:confirm="{{ __('Delete this shift template?') }}"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif

    </div>

    <flux:modal name="template-form" class="max-w-md">
        <form wire:submit="save" class="space-y-5">
            <flux:heading>
                {{ $editingId ? __('Edit Shift Template') : __('New Shift Template') }}
            </flux:heading>

            <flux:input
                wire:model="name"
                :label="__('Shift Name')"
                :placeholder="__('e.g. Morning Shift')"
                required
                autofocus
            />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="startTime" :label="__('Start Time')" type="time" required />
                <flux:input wire:model="endTime" :label="__('End Time')" type="time" required />
            </div>

            <flux:input
                wire:model="breakMinutes"
                :label="__('Break Duration (minutes)')"
                type="number"
                min="0"
                max="480"
            />

            <flux:select wire:model="color" :label="__('Color')">
                <flux:select.option value="blue">🔵 {{ __('Blue') }}</flux:select.option>
                <flux:select.option value="green">🟢 {{ __('Green') }}</flux:select.option>
                <flux:select.option value="amber">🟡 {{ __('Amber') }}</flux:select.option>
                <flux:select.option value="purple">🟣 {{ __('Purple') }}</flux:select.option>
                <flux:select.option value="rose">🔴 {{ __('Rose') }}</flux:select.option>
                <flux:select.option value="zinc">⚪ {{ __('Gray') }}</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ $editingId ? __('Update') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
