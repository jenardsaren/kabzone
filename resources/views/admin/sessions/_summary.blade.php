<div class="rounded-lg bg-white p-6 shadow-sm">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Session #{{ $session->id }}</h3>
        <a href="{{ route('admin.sessions.edit', $session) }}" class="text-sm text-indigo-600 hover:text-indigo-500">Override</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        <div>
            <p class="text-gray-500">Date</p>
            <p class="font-medium text-gray-900">{{ $session->date->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Time</p>
            <p class="font-medium text-gray-900">{{ $session->formatted_time }}</p>
        </div>
        <div>
            <p class="text-gray-500">Type</p>
            <p class="font-medium text-gray-900">{{ str($session->type->value)->headline() }}</p>
        </div>
        <div>
            <p class="text-gray-500">Client</p>
            <p class="font-medium text-gray-900">{{ $session->client?->full_name }}</p>
        </div>
        <div>
            <p class="text-gray-500">OT</p>
            <p class="font-medium text-gray-900">{{ $session->therapist?->full_name }}</p>
        </div>
        <div>
            <p class="text-gray-500">KSA</p>
            <p class="font-medium text-gray-900">{{ $session->assistant?->full_name ?? 'Unassigned' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Status</p>
            <p><x-status-badge :status="$session->status" /></p>
        </div>
    </div>

    <div class="mt-6 space-y-4 text-sm">
        <div>
            <p class="font-semibold text-gray-700">Description</p>
            <p class="mt-1 text-gray-700">{{ $session->description ?: 'No description.' }}</p>
        </div>
        <div class="hidden">
            <p class="font-semibold text-gray-700">Notes</p>
            <p class="mt-1 whitespace-pre-line text-gray-700">{{ $session->notes ?: 'No notes.' }}</p>
        </div>
    </div>

    <div class="mt-6">
        <h4 class="font-semibold text-gray-900">Tasks</h4>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                    <tr>
                        <th class="px-3 py-2">Task</th>
                        <th class="px-3 py-2">Description</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($session->tasks as $task)
                        <tr>
                            <td class="px-3 py-2">{{ $task->name }}</td>
                            <td class="px-3 py-2">{{ $task->description ?: '—' }}</td>
                            <td class="px-3 py-2"><x-status-badge :status="$task->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-6 text-center text-gray-500">No tasks yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <h4 class="font-semibold text-gray-900">Session Notes</h4>
        <div class="mt-3 divide-y divide-gray-200 rounded-md border border-gray-200">
                    <details open class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>Behavior Observations</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                <div class="mt-3">
                    @include('sessions.behavior-observations-form', [
                        'route' => route('admin.sessions.notes.update', $session),
                        'note' => $session->note,
                    ])
                </div>
            </details>

            <details open class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>Activities and Management</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                        <div class="mt-3">
                            <form method="POST" action="{{ route('admin.sessions.notes.update', $session) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="note_section" value="activities">

                                <x-input-label for="am_activities_and_management" :value="__('Activities and Management')" />
                        <textarea
                            id="am_activities_and_management"
                            name="am_activities_and_management"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('am_activities_and_management', $session->note?->am_activities_and_management) }}</textarea>
                        <x-input-error :messages="$errors->get('am_activities_and_management')" class="mt-2" />

                        <div class="flex justify-end">
                            <x-primary-button>Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </details>

            <details class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>EI Session Notes</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                <div class="mt-2">
                    @include('sessions.ei-session-notes-form', [
                        'route' => route('admin.sessions.notes.update', $session),
                        'note' => $session->note,
                    ])
                </div>
            </details>

            <details class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>EF Session Notes</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                <div class="mt-2">
                    @include('sessions.ef-session-notes-form', [
                        'route' => route('admin.sessions.notes.update', $session),
                        'note' => $session->note,
                    ])
                </div>
            </details>
            <details open class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>Plan</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                <div class="mt-2">
                    @include('sessions.plan-form', [
                        'route' => route('admin.sessions.notes.update', $session),
                        'note' => $session->note,
                    ])
                </div>
            </details>

            <details open class="group p-4">
                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-gray-700">
                    <span>Approval</span>
                    <svg class="h-4 w-4 text-gray-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </summary>
                <div class="mt-2">
                    @include('sessions.approval-form', [
                        'route' => route('admin.sessions.notes.update', $session),
                        'note' => $session->note,
                    ])
                </div>
            </details>
        </div>
    </div>
</div>
