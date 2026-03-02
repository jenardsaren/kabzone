<?php

namespace App\Http\Controllers\FrontDesk;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $recentSessions = Session::query()
            ->with(['client', 'therapist', 'assistant'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->whereHas('client', function (Builder $clientQuery) use ($search): void {
                            $clientQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('therapist', function (Builder $therapistQuery) use ($search): void {
                            $therapistQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->recent()
            ->paginate(50)
            ->withQueryString();

        return view('front-desk.dashboard', [
            'recentSessions' => $recentSessions,
            'search' => $search,
        ]);
    }
}
