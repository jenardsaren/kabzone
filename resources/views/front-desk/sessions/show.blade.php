@php
    $isPending = $session->status->value === 'pending';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Session Details</h2>
            <div class="flex items-center gap-3">
                @if ($isPending)
                    <a href="{{ route('front-desk.sessions.edit', $session) }}" class="text-sm text-indigo-600 hover:text-indigo-500">Edit Schedule</a>
                @endif
                <a href="{{ route('front-desk.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'session-updated')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Session schedule updated.
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
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
                        <p class="text-gray-500">OTPR</p>
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

                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Description</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $session->description ?: 'No description provided.' }}</p>
                    </div>

                    <div class="hidden">
                        <p class="text-sm font-semibold text-gray-700">Notes</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $session->notes ?: 'No notes yet.' }}</p>
                    </div>
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
    </div>
</x-app-layout>
