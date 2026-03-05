@props(['route', 'note' => null])

<form method="POST" action="{{ $route }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <input type="hidden" name="note_section" value="plan">

    <x-input-label for="plan" :value="__('Plan')" />
    <textarea
        id="plan"
        name="plan"
        rows="5"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
    >{{ old('plan', $note?->plan) }}</textarea>
    <x-input-error :messages="$errors->get('plan')" class="mt-2" />

    <div class="flex justify-end">
        <x-primary-button>Save</x-primary-button>
    </div>
</form>
