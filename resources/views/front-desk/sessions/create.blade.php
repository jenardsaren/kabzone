<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Create Session</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                Operating hours: Mon-Fri 8:00 AM-8:00 PM, Sun 1:00 PM-8:00 PM, Sat closed. Sessions are hourly slots.
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm" x-data="{ mode: @js(old('schedule_mode', 'single')), ...comboboxSetup() }">
                @php
                    $timeSlots = [];
                    foreach (range(8, 19) as $hour) {
                        $timeSlots[] = sprintf('%02d:00', $hour);
                    }
                    $selectedTime = old('time', '08:00');

                    $therapistOptions = $therapists->map(fn ($t) => ['id' => $t->id, 'label' => $t->full_name])->values();
                    $assistantOptions = collect([['id' => '', 'label' => 'Unassigned']])
                        ->merge($assistants->map(fn ($a) => ['id' => $a->id, 'label' => $a->full_name]))
                        ->values();
                    $clientOptions = $clients->map(fn ($c) => ['id' => $c->id, 'label' => $c->full_name])->values();
                @endphp
                <form method="POST" action="{{ route('front-desk.sessions.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="time" :value="__('Time')" />
                            <select id="time" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach ($timeSlots as $slot)
                                    <option value="{{ $slot }}" @selected($selectedTime === $slot)>
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Sessions always start on the hour; minutes are fixed.</p>
                            <x-input-error :messages="$errors->get('time')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="initial" @selected(old('type') === 'initial')>Initial</option>
                                <option value="regular" @selected(old('type', 'regular') === 'regular')>Regular</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="therapist_id" :value="__('OT')" />
                            <div class="relative" x-data="comboboxField({ options: @js($therapistOptions), initialId: @js(old('therapist_id')), placeholder: 'Select OT' })" @click.away="open = false">
                                <input type="hidden" name="therapist_id" x-model="selectedId" required>
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true; filter()"
                                        @input="filter()"
                                        @keydown.arrow-down.prevent="highlightNext()"
                                        @keydown.arrow-up.prevent="highlightPrev()"
                                        @keydown.enter.prevent="selectHighlighted()"
                                        @keydown.escape="open = false"
                                        placeholder="Select OT"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-autocomplete="list"
                                        :aria-expanded="open"
                                    >
                                    <button type="button" x-show="selectedId" @click="clear()" class="absolute inset-y-0 right-2 text-gray-400 hover:text-gray-600" aria-label="Clear selection">
                                        &times;
                                    </button>
                                </div>
                                <div
                                    x-show="open"
                                    x-cloak
                                    class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg"
                                    @mousedown.prevent
                                >
                                    <ul class="max-h-48 overflow-y-auto text-sm">
                                        <template x-for="(option, index) in filtered" :key="option.id">
                                            <li>
                                                <button
                                                    type="button"
                                                    @click="select(option)"
                                                    :class="['w-full text-left px-3 py-2 hover:bg-gray-100', highlightedIndex === index ? 'bg-gray-100' : '']"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </li>
                                        </template>
                                        <li x-show="!filtered.length" class="px-3 py-2 text-xs text-gray-500">No results</li>
                                    </ul>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('therapist_id')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="assistant_id" :value="__('KSA')" />
                            <div class="relative" x-data="comboboxField({ options: @js($assistantOptions), initialId: @js(old('assistant_id')), placeholder: 'Unassigned' })" @click.away="open = false">
                                <input type="hidden" name="assistant_id" x-model="selectedId">
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true; filter()"
                                        @input="filter()"
                                        @keydown.arrow-down.prevent="highlightNext()"
                                        @keydown.arrow-up.prevent="highlightPrev()"
                                        @keydown.enter.prevent="selectHighlighted()"
                                        @keydown.escape="open = false"
                                        placeholder="Unassigned"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-autocomplete="list"
                                        :aria-expanded="open"
                                    >
                                    <button type="button" x-show="selectedId" @click="clear()" class="absolute inset-y-0 right-2 text-gray-400 hover:text-gray-600" aria-label="Clear selection">
                                        &times;
                                    </button>
                                </div>
                                <div
                                    x-show="open"
                                    x-cloak
                                    class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg"
                                    @mousedown.prevent
                                >
                                    <ul class="max-h-48 overflow-y-auto text-sm">
                                        <template x-for="(option, index) in filtered" :key="option.id + '-assistant'">
                                            <li>
                                                <button
                                                    type="button"
                                                    @click="select(option)"
                                                    :class="['w-full text-left px-3 py-2 hover:bg-gray-100', highlightedIndex === index ? 'bg-gray-100' : '']"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </li>
                                        </template>
                                        <li x-show="!filtered.length" class="px-3 py-2 text-xs text-gray-500">No results</li>
                                    </ul>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('assistant_id')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="client_id" :value="__('Client')" />
                            <div class="relative" x-data="comboboxField({ options: @js($clientOptions), initialId: @js(old('client_id')), placeholder: 'Select client' })" @click.away="open = false">
                                <input type="hidden" name="client_id" x-model="selectedId" required>
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true; filter()"
                                        @input="filter()"
                                        @keydown.arrow-down.prevent="highlightNext()"
                                        @keydown.arrow-up.prevent="highlightPrev()"
                                        @keydown.enter.prevent="selectHighlighted()"
                                        @keydown.escape="open = false"
                                        placeholder="Select client"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        aria-autocomplete="list"
                                        :aria-expanded="open"
                                    >
                                    <button type="button" x-show="selectedId" @click="clear()" class="absolute inset-y-0 right-2 text-gray-400 hover:text-gray-600" aria-label="Clear selection">
                                        &times;
                                    </button>
                                </div>
                                <div
                                    x-show="open"
                                    x-cloak
                                    class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg"
                                    @mousedown.prevent
                                >
                                    <ul class="max-h-48 overflow-y-auto text-sm">
                                        <template x-for="(option, index) in filtered" :key="option.id + '-client'">
                                            <li>
                                                <button
                                                    type="button"
                                                    @click="select(option)"
                                                    :class="['w-full text-left px-3 py-2 hover:bg-gray-100', highlightedIndex === index ? 'bg-gray-100' : '']"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </li>
                                        </template>
                                        <li x-show="!filtered.length" class="px-3 py-2 text-xs text-gray-500">No results</li>
                                    </ul>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="payment_status" :value="__('Payment Status')" />
                            <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="Unpaid" @selected(old('payment_status', 'Unpaid') === 'Unpaid')>Unpaid</option>
                                <option value="Paid" @selected(old('payment_status') === 'Paid')>Paid</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Mark whether the session has already been paid.</p>
                            <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Schedule Mode')" />
                        <div class="mt-2 flex gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="radio" name="schedule_mode" value="single" x-model="mode" @checked(old('schedule_mode', 'single') === 'single')>
                                Single
                            </label>
                            <label class="hidden inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="radio" name="schedule_mode" value="repeat" x-model="mode" @checked(old('schedule_mode') === 'repeat')>
                                Repeat Daily
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="radio" name="schedule_mode" value="repeat_weekly" x-model="mode" @checked(old('schedule_mode') === 'repeat_weekly')>
                                Repeat Weekly
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('schedule_mode')" class="mt-2" />
                    </div>

                    <div x-show="mode !== 'single'" x-cloak>
                        <x-input-label for="repeat_days" :value="__('Repeat Days (max 30)')" x-show="mode === 'repeat'" />
                        <x-input-label for="repeat_days" :value="__('Repeat Weeks (max 12)')" x-show="mode === 'repeat_weekly'" />
                        <x-text-input
                            id="repeat_days"
                            name="repeat_days"
                            type="number"
                            min="1"
                            x-bind:max="mode === 'repeat_weekly' ? 12 : 30"
                            class="mt-1 block w-full"
                            :value="old('repeat_days', 7)"
                        />
                        <x-input-error :messages="$errors->get('repeat_days')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Create Session</x-primary-button>
                        <a href="{{ route('front-desk.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            window.comboboxSetup = () => ({});

            Alpine.data('comboboxField', ({ options, initialId = '', placeholder = '' }) => ({
                options,
                filtered: options,
                search: '',
                selectedId: initialId ?? '',
                open: false,
                highlightedIndex: 0,

                init() {
                    const current = this.options.find((option) => option.id == this.selectedId);

                    if (current) {
                        this.search = current.label;
                    }
                },

                filter() {
                    const term = this.search.toLowerCase();
                    this.filtered = this.options.filter((option) => option.label.toLowerCase().includes(term));
                    this.highlightedIndex = 0;
                    this.open = true;
                },

                highlightNext() {
                    if (!this.filtered.length) {
                        return;
                    }

                    this.highlightedIndex = (this.highlightedIndex + 1) % this.filtered.length;
                },

                highlightPrev() {
                    if (!this.filtered.length) {
                        return;
                    }

                    this.highlightedIndex = (this.highlightedIndex - 1 + this.filtered.length) % this.filtered.length;
                },

                selectHighlighted() {
                    if (!this.filtered.length) {
                        return;
                    }

                    this.select(this.filtered[this.highlightedIndex]);
                },

                select(option) {
                    this.selectedId = option.id;
                    this.search = option.label;
                    this.open = false;
                },

                clear() {
                    this.selectedId = '';
                    this.search = '';
                    this.filtered = this.options;
                },
            }));
        });
    </script>
</x-app-layout>
