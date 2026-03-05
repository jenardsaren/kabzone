<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Admin Override Session</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'session-overridden')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Session override saved.
                </div>
            @endif

            @include('admin.sessions._summary', ['session' => $session])

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Override Fields</h3>

                <form method="POST" action="{{ route('admin.sessions.update', $session) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="assistant_id" :value="__('KSA')" />
                        <select id="assistant_id" name="assistant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Unassigned</option>
                            @foreach ($assistants as $assistant)
                                <option value="{{ $assistant->id }}" @selected((int) old('assistant_id', $session->assistant_id) === $assistant->id)>
                                    {{ $assistant->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assistant_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="pending" @selected(old('status', $session->status->value) === 'pending')>Pending</option>
                            <option value="completed" @selected(old('status', $session->status->value) === 'completed')>Completed</option>
                            <option value="cancelled" @selected(old('status', $session->status->value) === 'cancelled')>Cancelled</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $session->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="hidden">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $session->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <x-primary-button>Save Override</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
