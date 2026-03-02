<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Client Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Completed Sessions</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-3 py-2">Date</th>
                                <th class="px-3 py-2">Time</th>
                                <th class="px-3 py-2">Therapist</th>
                                <th class="px-3 py-2">Assistant</th>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($sessions as $session)
                                <tr>
                                    <td class="px-3 py-2">{{ $session->date->format('M d, Y') }}</td>
                                    <td class="px-3 py-2">{{ $session->formatted_time }}</td>
                                    <td class="px-3 py-2">{{ $session->therapist?->full_name }}</td>
                                    <td class="px-3 py-2">{{ $session->assistant?->full_name ?? 'Unassigned' }}</td>
                                    <td class="px-3 py-2">{{ str($session->type->value)->headline() }}</td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('client.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-500">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">No completed sessions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
