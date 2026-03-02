<?php

namespace App\Http\Controllers\FrontDesk;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontDesk\StoreClientRequest;
use App\Http\Requests\FrontDesk\UpdateClientRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $clients = User::query()
            ->where('role', UserRole::Client->value)
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

        return view('front-desk.clients.index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('front-desk.clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $temporaryPassword = Str::password(12);

        $client = User::query()->create([
            ...$request->validated(),
            'role' => UserRole::Client,
            'status' => UserStatus::Active,
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('front-desk.clients.edit', $client)
            ->with('status', 'client-created')
            ->with('temporary_password', $temporaryPassword);
    }

    public function edit(User $client): View
    {
        $this->authorize('updateClient', $client);

        return view('front-desk.clients.edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('front-desk.clients.edit', $client)
            ->with('status', 'client-updated');
    }
}
