@php
    $isPending = $session->status->value === 'pending';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Therapist Session View</h2>
            <a href="{{ route('therapist.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ str_replace('-', ' ', session('status')) }}.
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-sm">
                    <div>
                        <p class="text-gray-500">Date</p>
                        <p class="font-medium text-gray-900">{{ $session->date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Time</p>
                        <p class="font-medium text-gray-900">{{ $session->formatted_time }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Client</p>
                        <p class="font-medium text-gray-900">{{ $session->client?->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p><x-status-badge :status="$session->status" /></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Session Details</h3>

                    <div class="mt-4">
                        <p class="text-sm font-semibold text-gray-700">Assistant</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $session->assistant?->full_name ?? 'Unassigned' }}</p>
                    </div>

                    <form method="POST" action="{{ route('therapist.sessions.details.update', $session) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>{{ old('description', $session->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>{{ old('notes', $session->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <x-primary-button :disabled="! $isPending">Save Details</x-primary-button>
                    </form>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Add Task</h3>

                    <form method="POST" action="{{ route('therapist.sessions.tasks.store', $session) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Task Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required :disabled="! $isPending" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="task_description" :value="__('Description')" />
                            <textarea id="task_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled(! $isPending)>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <x-primary-button :disabled="! $isPending">Add Task</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Tasks</h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-3 py-2">Task</th>
                                <th class="px-3 py-2">Description</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($session->tasks as $task)
                                <tr>
                                    <td class="px-3 py-2">{{ $task->name }}</td>
                                    <td class="px-3 py-2">{{ $task->description ?: '—' }}</td>
                                    <td class="px-3 py-2"><x-status-badge :status="$task->status" /></td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('therapist.sessions.tasks.update', [$session, $task]) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="name" value="{{ $task->name }}" class="w-40 rounded-md border-gray-300 text-xs shadow-sm" @disabled(! $isPending) required>
                                            <input type="text" name="description" value="{{ $task->description }}" class="w-56 rounded-md border-gray-300 text-xs shadow-sm" @disabled(! $isPending)>
                                            <button type="submit" class="rounded bg-gray-800 px-2 py-1 text-xs font-semibold text-white hover:bg-gray-700" @disabled(! $isPending)>
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-500">No tasks yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Session Notes</h3>

                <div class="mt-4 divide-y divide-gray-200 rounded-md border border-gray-200">
                    <details class="group p-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                            <span>Behavior Observations</span>
                            <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="mt-3 text-sm text-gray-700">No notes yet.</div>
                    </details>

                    <details class="group p-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                            <span>EI Session Notes</span>
                            <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="mt-3 text-sm text-gray-700">No notes yet.</div>
                    </details>

                    <details class="group p-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                            <span>EF Session Notes</span>
                            <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </summary>
                        <div class="mt-3 text-sm text-gray-700">No notes yet.</div>
                    </details>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Status Transition</h3>

                <div class="mt-4 flex gap-3">
                    <form method="POST" action="{{ route('therapist.sessions.status.update', $session) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500" @disabled(! $isPending)>
                            Mark Completed
                        </button>
                    </form>

                    <form method="POST" action="{{ route('therapist.sessions.status.update', $session) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500" @disabled(! $isPending)>
                            Cancel Session
                        </button>
                    </form>
                </div>

                @if (! $isPending)
                    <p class="mt-3 text-sm text-gray-600">Completed and cancelled sessions are locked.</p>
                @endif

                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                <x-input-error :messages="$errors->get('session')" class="mt-2" />
            </div>
        </div>
    </div>
</x-app-layout>
