<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\User;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $query = LeaveRequest::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Filter for approved leave requests
        $query->where('status', 'approuvé');

        $reports = $query->with('user')->get();
        $users = User::all();

        return Inertia::render('Admin/Reports/ReportView', [
            'reports' => $reports,
            'users' => $users
        ]);
    }

    // New method to send data to another view
    public function sendDataToAnotherView(Request $request)
    {
        $query = LeaveRequest::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Filter for approved leave requests
        $query->where('status', 'approuvé');

        $reports = $query->with('user')->get();
        $users = User::all();

        return Inertia::render('Admin/Reports/Results', [
            'reports' => $reports,
            'users' => $users
        ]);
    }
}
