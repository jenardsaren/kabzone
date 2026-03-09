@php
    $isPending = $session->status->value === 'pending';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Edit Scheduled Session</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (! $isPending)
                <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Only pending sessions can be edited by Front Desk.
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('front-desk.sessions.update', $session) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $session->date->format('Y-m-d'))" required :disabled="! $isPending" />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="time" :value="__('Time')" />
                            <x-text-input id="time" name="time" type="time" class="mt-1 block w-full" step="3600" :value="old('time', substr((string) $session->time, 0, 5))" required :disabled="! $isPending" />
                            <x-input-error :messages="$errors->get('time')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled(! $isPending)>
                                <option value="initial" @selected(old('type', $session->type->value) === 'initial')>Initial</option>
                                <option value="regular" @selected(old('type', $session->type->value) === 'regular')>Regular</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="therapist_id" :value="__('OT')" />
                            <select id="therapist_id" name="therapist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled(! $isPending)>
                                <option value="">Select OT</option>
                                @foreach ($therapists as $therapist)
                                    <option value="{{ $therapist->id }}" @selected((int) old('therapist_id', $session->therapist_id) === $therapist->id)>
                                        {{ $therapist->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('therapist_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="assistant_id" :value="__('KSA')" />
                            <select id="assistant_id" name="assistant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>
                                <option value="">Unassigned</option>
                                @foreach ($assistants as $assistant)
                                    <option value="{{ $assistant->id }}" @selected((int) old('assistant_id', $session->assistant_id) === $assistant->id)>
                                        {{ $assistant->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assistant_id')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="client_id" :value="__('Client')" />
                            <select id="client_id" name="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled(! $isPending)>
                                <option value="">Select client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected((int) old('client_id', $session->client_id) === $client->id)>
                                        {{ $client->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>{{ old('description', $session->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="payment_status" :value="__('Payment Status')" />
                            <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>
                                <option value="Unpaid" @selected(old('payment_status', $session->payment_status ?? 'Unpaid') === 'Unpaid')>Unpaid</option>
                                <option value="Paid" @selected(old('payment_status', $session->payment_status) === 'Paid')>Paid</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button :disabled="! $isPending">Save Schedule</x-primary-button>
                        <a href="{{ route('front-desk.sessions.show', $session) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
