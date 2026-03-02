@csrf

@if ($method === 'PATCH')
    @method('PATCH')
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $staff?->first_name)" required />
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="middle_name" :value="__('Middle Name')" />
        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $staff?->middle_name)" />
        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $staff?->last_name)" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $staff?->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="contact_no" :value="__('Contact Number')" />
        <x-text-input id="contact_no" name="contact_no" type="text" class="mt-1 block w-full" :value="old('contact_no', $staff?->contact_no)" required />
        <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="gender" :value="__('Gender')" />
        <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="male" @selected(old('gender', $staff?->gender?->value ?? $staff?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $staff?->gender?->value ?? $staff?->gender) === 'female')>Female</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="role" :value="__('Role')" />
        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $staff?->role?->value ?? $staff?->role) === $role)>
                    {{ str($role)->headline() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="active" @selected(old('status', $staff?->status?->value ?? $staff?->status) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $staff?->status?->value ?? $staff?->status) === 'inactive')>Inactive</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $staff?->address)" required />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('admin.staff.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>
