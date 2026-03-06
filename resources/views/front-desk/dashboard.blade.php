<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Front Desk Dashboard</h2>
                <p class="text-sm text-gray-600">Schedule sessions and monitor recent activity.</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('front-desk.clients.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                    Register Client
                </a>
                <a href="{{ route('front-desk.sessions.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Create Session
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'session-scheduled')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Created {{ session('scheduled_count', 0) }} session(s).
                </div>
            @endif

            @if (is_array(session('skipped_dates')) && count(session('skipped_dates')) > 0)
                <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <p class="font-semibold">Skipped dates summary</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach (session('skipped_dates') as $skippedDate)
                            <li>{{ $skippedDate['date'] }} ({{ str($skippedDate['reason'])->replace('_', ' ') }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <div id="front-desk-calendar"></div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Sessions</h3>

                    <form method="GET" action="{{ route('front-desk.dashboard') }}" class="flex gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search client or OT"
                            class="w-64 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button type="submit" class="rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            Search
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-3 py-2">Date</th>
                                <th class="px-3 py-2">Time</th>
                                <th class="px-3 py-2">Client</th>
                                <th class="px-3 py-2">OT</th>
                                <th class="px-3 py-2">KSA</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($recentSessions as $session)
                                <tr>
                                    <td class="px-3 py-2">{{ $session->date->format('M d, Y') }}</td>
                                    <td class="px-3 py-2">{{ $session->formatted_time }}</td>
                                    <td class="px-3 py-2">{{ $session->client?->full_name }}</td>
                                    <td class="px-3 py-2">{{ $session->therapist?->full_name }}</td>
                                    <td class="px-3 py-2">{{ $session->assistant?->full_name ?? 'Unassigned' }}</td>
                                    <td class="px-3 py-2"><x-status-badge :status="$session->status" /></td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('front-desk.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-500">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-gray-500">No sessions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $recentSessions->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const element = document.getElementById('front-desk-calendar');

                if (! element) {
                    return;
                }

                const calendar = new FullCalendar.Calendar(element, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay',
                    },
                    events: '{{ route('front-desk.calendar-events') }}',
                });

                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
