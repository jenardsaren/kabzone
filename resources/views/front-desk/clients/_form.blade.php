@csrf

@if ($method === 'PATCH')
    @method('PATCH')
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $client?->first_name)" required />
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="middle_name" :value="__('Middle Name')" />
        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $client?->middle_name)" />
        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $client?->last_name)" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $client?->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="contact_no" :value="__('Contact Number')" />
        <x-text-input id="contact_no" name="contact_no" type="text" class="mt-1 block w-full" :value="old('contact_no', $client?->contact_no)" required />
        <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="gender" :value="__('Gender')" />
        <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Select gender</option>
            <option value="male" @selected(old('gender', $client?->gender?->value ?? $client?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $client?->gender?->value ?? $client?->gender) === 'female')>Female</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
        <x-text-input
            id="date_of_birth"
            name="date_of_birth"
            type="date"
            class="mt-1 block w-full"
            :value="old('date_of_birth', $client?->date_of_birth?->format('Y-m-d'))"
            required
        />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="age" :value="__('Age')" />
        <x-text-input
            id="age"
            name="age"
            type="number"
            min="0"
            max="120"
            inputmode="numeric"
            class="mt-1 block w-full"
            :value="old('age', $client?->age)"
            readonly
        />
        <p class="mt-1 text-xs text-gray-500">Automatically calculated from the date of birth.</p>
        <x-input-error :messages="$errors->get('age')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $client?->address)" required />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="parent_name" :value="__('Parent Name')" />
        <x-text-input id="parent_name" name="parent_name" type="text" class="mt-1 block w-full" :value="old('parent_name', $client?->parent_name)" required />
        <x-input-error :messages="$errors->get('parent_name')" class="mt-2" />
    </div>

    @if ($showStatus)
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="active" @selected(old('status', $client?->status?->value ?? $client?->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $client?->status?->value ?? $client?->status) === 'inactive')>Inactive</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    @endif
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('front-desk.clients.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>
