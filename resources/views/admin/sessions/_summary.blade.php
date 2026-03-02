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
            <p class="text-gray-500">Therapist</p>
            <p class="font-medium text-gray-900">{{ $session->therapist?->full_name }}</p>
        </div>
        <div>
            <p class="text-gray-500">Assistant</p>
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
        <div>
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
</div>
