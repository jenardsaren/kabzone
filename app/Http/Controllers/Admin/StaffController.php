<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $staff = User::query()
            ->whereIn('role', [
                UserRole::Admin->value,
                UserRole::Therapist->value,
                UserRole::Assistant->value,
                UserRole::FrontDesk->value,
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.staff.index', [
            'staff' => $staff,
            'search' => $search,
            'roles' => [
                UserRole::Admin->value,
                UserRole::Therapist->value,
                UserRole::Assistant->value,
                UserRole::FrontDesk->value,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.staff.create', [
            'roles' => [
                UserRole::Admin->value,
                UserRole::Therapist->value,
                UserRole::Assistant->value,
                UserRole::FrontDesk->value,
            ],
        ]);
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $temporaryPassword = Str::password(12);

        $staff = User::query()->create([
            ...$request->validated(),
            'password' => $temporaryPassword,
            'must_change_password' => true,
            'parent_name' => null,
        ]);

        return redirect()
            ->route('admin.staff.edit', $staff)
            ->with('status', 'staff-created')
            ->with('temporary_password', $temporaryPassword);
    }

    public function edit(User $staff): View
    {
        $this->authorize('updateStaff', $staff);

        return view('admin.staff.edit', [
            'staff' => $staff,
            'roles' => [
                UserRole::Admin->value,
                UserRole::Therapist->value,
                UserRole::Assistant->value,
                UserRole::FrontDesk->value,
            ],
        ]);
    }

    public function update(UpdateStaffRequest $request, User $staff): RedirectResponse
    {
        $staff->update([
            ...$request->validated(),
            'parent_name' => null,
        ]);

        return redirect()
            ->route('admin.staff.edit', $staff)
            ->with('status', 'staff-updated');
    }
}
