<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Services\AttendanceMetricsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, AttendanceMetricsService $attendanceMetricsService): View
    {
        $metrics = $attendanceMetricsService->getMetrics();
        $search = trim($request->string('search')->toString());

        $sessions = Session::query()
            ->with(['client', 'therapist', 'assistant', 'tasks'])
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

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'sessions' => $sessions,
            'search' => $search,
        ]);
    }
}
