    <div class="flex h-full w-full flex-1 flex-col gap-4" x-data="{ dragging: null }">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('Weekly Schedule') }}</flux:heading>
                @if ($this->schedule?->isPublished())
                    <flux:badge color="green" icon="check-circle">{{ __('Published') }}</flux:badge>
                @else
                    <flux:badge color="zinc">{{ __('Draft') }}</flux:badge>
                @endif
            </div>

            <div class="flex items-center gap-2">
                {{-- Branch selector --}}
                @if ($this->branches->count() > 1)
                    <flux:select wire:model.live="branchId" class="w-40">
                        @foreach ($this->branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                {{-- Week navigation --}}
                <flux:button icon="chevron-left" wire:click="previousWeek" />
                <span class="min-w-40 text-center text-sm font-medium">
                    {{ Carbon\Carbon::parse($weekStart)->format('M d') }} –
                    {{ Carbon\Carbon::parse($weekStart)->addDays(6)->format('M d, Y') }}
                </span>
                <flux:button icon="chevron-right" wire:click="nextWeek" />

                {{-- Publish --}}
                @if ($this->schedule?->isPublished())
                    <flux:button wire:click="unpublish" wire:confirm="{{ __('Unpublish this schedule?') }}">
                        {{ __('Unpublish') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" wire:click="publish">
                        {{ __('Publish') }}
                    </flux:button>
                @endif
            </div>
        </div>

        {{-- Shift template palette (draggable) --}}
        @if ($this->shiftTemplates->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <flux:text class="self-center text-xs font-medium uppercase tracking-wide text-zinc-400">
                    {{ __('Drag to assign:') }}
                </flux:text>
                @foreach ($this->shiftTemplates as $template)
                    @php $c = $template->colorClasses(); @endphp
                    <div
                        draggable="true"
                        @dragstart="dragging = {{ $template->id }}; $event.dataTransfer.setData('templateId', {{ $template->id }})"
                        @dragend="dragging = null"
                        class="cursor-grab select-none rounded-full border px-3 py-1 text-xs font-semibold transition active:cursor-grabbing {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }}"
                        title="{{ $template->formattedHours() }}"
                    >
                        {{ $template->name }}
                    </div>
                @endforeach
            </div>
        @else
            <flux:callout icon="information-circle" color="blue">
                <flux:callout.heading>{{ __('No shift templates') }}</flux:callout.heading>
                <flux:callout.text>
                    <a href="{{ route('shift-templates.index') }}" wire:navigate class="underline">
                        {{ __('Create shift templates') }}
                    </a>
                    {{ __('before building a schedule.') }}
                </flux:callout.text>
            </flux:callout>
        @endif

        {{-- Schedule Grid --}}
        @if ($this->employees->isEmpty())
            <flux:card class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon.users class="mb-4 size-10 text-zinc-400" />
                <flux:heading>{{ __('No employees yet') }}</flux:heading>
                <flux:text class="mt-1">
                    <a href="{{ route('employees.invite') }}" wire:navigate class="underline">{{ __('Invite employees') }}</a>
                    {{ __('to start scheduling.') }}
                </flux:text>
            </flux:card>
        @else
            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="w-full min-w-[700px] table-fixed text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                            <th class="w-36 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">
                                {{ __('Employee') }}
                            </th>
                            @foreach ($this->weekDates as $date)
                                <th class="px-2 py-3 text-center font-medium {{ $date->isToday() ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    <div>{{ $date->format('D') }}</div>
                                    <div class="text-xs font-normal">{{ $date->format('M d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($this->employees as $member)
                            <tr class="group">
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <flux:avatar size="sm" :name="$member->user->name" :initials="$member->user->initials()" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium">{{ $member->user->name }}</p>
                                            @if ($member->position)
                                                <p class="truncate text-xs text-zinc-400">{{ $member->position }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                @foreach ($this->weekDates as $date)
                                    @php
                                        $dateStr = $date->toDateString();
                                        $entry = $this->entries[$member->user_id][$dateStr] ?? null;
                                        $template = $entry?->shiftTemplate;
                                        $c = $template ? $template->colorClasses() : null;
                                    @endphp
                                    <td
                                        class="px-1 py-1.5 text-center"
                                        @dragover.prevent
                                        @drop.prevent="$wire.assign($event.dataTransfer.getData('templateId'), {{ $member->user_id }}, '{{ $dateStr }}')"
                                    >
                                        @if ($entry && $template)
                                            <div
                                                class="group/cell relative mx-auto flex min-h-[2.5rem] cursor-pointer items-center justify-center rounded-lg border px-1 text-xs font-semibold {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }}"
                                                wire:click="removeEntry({{ $member->user_id }}, '{{ $dateStr }}')"
                                                title="{{ $template->formattedHours() }} – {{ __('Click to remove') }}"
                                            >
                                                <span class="group-hover/cell:hidden">{{ $template->name }}</span>
                                                <flux:icon.x-mark class="hidden size-4 group-hover/cell:block" />
                                            </div>
                                        @else
                                            <div
                                                class="mx-auto min-h-[2.5rem] rounded-lg border-2 border-dashed border-transparent transition hover:border-zinc-300 dark:hover:border-zinc-600"
                                                title="{{ __('Drop a shift here') }}"
                                            ></div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <flux:text class="text-center text-xs text-zinc-400">
                {{ __('Drag a shift onto a cell to assign. Click an assigned shift to remove it.') }}
            </flux:text>
        @endif

    </div>
