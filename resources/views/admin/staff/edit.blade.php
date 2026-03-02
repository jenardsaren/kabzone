<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Edit Staff Account</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('temporary_password'))
                <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Temporary password: <span class="font-semibold">{{ session('temporary_password') }}</span>
                </div>
            @endif

            @if (session('status') === 'staff-updated')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Staff account updated.
                </div>
            @endif

            @if (session('status') === 'staff-created')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Staff account created.
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.staff.update', $staff) }}">
                    @php
                        $method = 'PATCH';
                        $submitLabel = 'Update Staff';
                    @endphp

                    @include('admin.staff._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
