<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">KSA Session View</h2>
            <a href="{{ route('assistant.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'task-status-updated')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Task status updated.
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
                        <p class="text-gray-500">OT</p>
                        <p class="font-medium text-gray-900">{{ $session->therapist?->full_name }}</p>
                    </div>
                </div>

                <div class="mt-6 text-sm">
                    <p class="font-semibold text-gray-700">Description</p>
                    <p class="mt-1 text-gray-700">{{ $session->description ?: 'No description.' }}</p>
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
                                        <form method="POST" action="{{ route('assistant.tasks.status.update', $task) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="pending" @selected($task->status->value === 'pending')>Pending</option>
                                                <option value="completed" @selected($task->status->value === 'completed')>Completed</option>
                                            </select>
                                            <button type="submit" class="rounded bg-gray-800 px-2 py-1 text-xs font-semibold text-white hover:bg-gray-700">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-500">No tasks to update.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
