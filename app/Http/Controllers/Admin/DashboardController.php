<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Services\AttendanceMetricsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, AttendanceMetricsService $attendanceMetricsService): View
    {
        $metrics = $attendanceMetricsService->getMetrics();
        $today = Carbon::today();
        $search = trim($request->string('search')->toString());

        $searchFilter = static function (Builder $query) use ($search): void {
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
                    })
                    ->orWhereHas('assistant', function (Builder $assistantQuery) use ($search): void {
                        $assistantQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        };

        $todaySessions = Session::query()
            ->with(['client', 'therapist', 'assistant', 'tasks'])
            ->when($search !== '', $searchFilter)
            ->whereDate('date', $today)
            ->recent()
            ->paginate(50, ['*'], 'today_page')
            ->withQueryString();

        $upcomingSessions = Session::query()
            ->with(['client', 'therapist', 'assistant', 'tasks'])
            ->when($search !== '', $searchFilter)
            ->whereDate('date', '>', $today)
            ->recent()
            ->paginate(50, ['*'], 'upcoming_page')
            ->withQueryString();

        $pastSessions = Session::query()
            ->with(['client', 'therapist', 'assistant', 'tasks'])
            ->when($search !== '', $searchFilter)
            ->whereDate('date', '<', $today)
            ->recent()
            ->paginate(50, ['*'], 'past_page')
            ->withQueryString();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'todaySessions' => $todaySessions,
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
            'search' => $search,
        ]);
    }
}
