    <div class="flex min-h-full flex-1 items-center justify-center py-16">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <flux:heading size="xl">Welcome to Manage Right</flux:heading>
                <flux:text class="mt-2">Tell us about your business to get started.</flux:text>
            </div>

            <flux:card>
                <form wire:submit="save" class="space-y-6">
                    <flux:input
                        wire:model="name"
                        :label="__('Business Name')"
                        :placeholder="__('e.g. Brew & Co.')"
                        required
                        autofocus
                    />

                    <flux:select wire:model="type" :label="__('Business Type')" required>
                        <flux:select.option value="">{{ __('Select a type…') }}</flux:select.option>
                        <flux:select.option value="Café">{{ __('Café') }}</flux:select.option>
                        <flux:select.option value="Restaurant">{{ __('Restaurant') }}</flux:select.option>
                        <flux:select.option value="Salon">{{ __('Salon') }}</flux:select.option>
                        <flux:select.option value="Retail Store">{{ __('Retail Store') }}</flux:select.option>
                        <flux:select.option value="Kiosk">{{ __('Kiosk') }}</flux:select.option>
                        <flux:select.option value="Clinic">{{ __('Clinic') }}</flux:select.option>
                        <flux:select.option value="Other">{{ __('Other') }}</flux:select.option>
                    </flux:select>

                    <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Create Business') }}</span>
                        <span wire:loading>{{ __('Creating…') }}</span>
                    </flux:button>
                </form>
            </flux:card>
        </div>
    </div>
