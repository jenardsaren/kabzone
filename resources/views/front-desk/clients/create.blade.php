<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Register Client</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('front-desk.clients.store') }}">
                    @php
                        $client = null;
                        $method = 'POST';
                        $showStatus = false;
                        $submitLabel = 'Create Client';
                    @endphp

                    @include('front-desk.clients._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
