<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">OT Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('therapist.partials.sessions-table', [
                'title' => "Today's Sessions",
                'sessions' => $todaySessions,
                'emptyMessage' => 'No sessions scheduled for today.',
            ])

            @include('therapist.partials.sessions-table', [
                'title' => 'Upcoming Sessions (Starting Tomorrow)',
                'sessions' => $upcomingSessions,
                'emptyMessage' => 'No upcoming sessions starting tomorrow.',
            ])

            @include('therapist.partials.sessions-table', [
                'title' => 'Past Sessions (Starting Yesterday)',
                'sessions' => $pastSessions,
                'emptyMessage' => 'No past sessions starting yesterday.',
            ])
        </div>
    </div>
</x-app-layout>
