@props(['route', 'note' => null])

<form method="POST" action="{{ $route }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <input type="hidden" name="note_section" value="behavior">
    <div x-data="{ showOther: {{ $note?->bo_other ? 'true' : 'false' }} }">
        <p class="text-sm font-semibold tracking-wide text-gray-600">Observations</p>

        <div class="mt-3 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                'bo_cooperative' => 'Cooperative',
                'bo_calm_regulated' => 'Calm/Regulated',
                'bo_restless_fidgety' => 'Restless/Fidgety',
                'bo_easily_frustrated' => 'Easily Frustrated',
                'bo_tantrums' => 'Tantrums',
                'bo_meltdowns' => 'Meltdowns',
                'bo_avoidant' => 'Avoidant',
                'bo_aggressive' => 'Aggressive',
            ] as $field => $label)
                <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">
                    <input
                        type="checkbox"
                        name="{{ $field }}"
                        value="1"
                        @checked(old($field, $note?->{$field}))
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    {{ $label }}
                </label>
            @endforeach

            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">
                <input
                    type="checkbox"
                    name="bo_other"
                    value="1"
                    x-model="showOther"
                    @checked(old('bo_other', $note?->bo_other))
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                Other
            </label>
        </div>

            <div class="mt-4" x-show="showOther" x-cloak>
                <x-input-label for="other_details" :value="__('Other Details')" />
                <textarea
                    id="other_details"
                    name="bo_other_details"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('bo_other_details', $note?->bo_other_details) }}</textarea>
            <x-input-error :messages="$errors->get('bo_other_details')" class="mt-2" />
            </div>

        <div class="flex justify-end">
            <x-primary-button>Save Observations</x-primary-button>
        </div>
    </div>
</form>
