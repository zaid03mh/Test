<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Notifications\LeaveRequestSubmitted;
use App\Notifications\LeaveRequestProcessed;
use Illuminate\Support\Facades\Log; 

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('LeaveRequests/LeaveRequestsHistory', ['requests' => $requests]);
    }
    
    public function adminIndex()
    {
        $allRequests = LeaveRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return Inertia::render('Admin/LeaveRequests/LeaveRequestsList', [
            'allRequests' => $allRequests
        ]);
    }
    public function adminDashboard()
    {
        $totalRequests = LeaveRequest::count();
        $processedRequests = LeaveRequest::where('processed', true)->count();
        $pendingRequests = LeaveRequest::where('processed', false)->count();
        $employees = Employee::all();
    
        return Inertia::render('Admin/Dashboard', [
            'totalRequests' => $totalRequests,
            'processedRequests' => $processedRequests,
            'pendingRequests' => $pendingRequests,
            'employees' => $employees,
        ]);
    }
    


    public function showAdmin(LeaveRequest $leaveRequest)
{
    $leaveRequest->load('user');
    return Inertia::render('Admin/LeaveRequests/LeaveRequestDetails', [
        'leaveRequest' => $leaveRequest
    ]);
}

    public function create()
    {
        return Inertia::render('LeaveRequests/LeaveRequestForm');
    }
    
    public function getLeaveBalance()
    {
        $employee = Employee::where('email', auth()->user()->email)->first();
        if ($employee) {
            return response()->json(['balance' => $employee->total_accumulated_leave]);
        } else {
            return response()->json(['balance' => 0], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string'
        ]);
    
        $daysRequested = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $employee = Employee::where('email', $request->user()->email)->first();
    
        if ($employee && $employee->total_accumulated_leave < $daysRequested) {
            return redirect()->back()->with('error', 'Votre solde n\'est pas suffisant.');
        }
    
        $leaveRequest = $request->user()->leaveRequests()->create($request->all());
    
        $leaveRequest->calculateDaysRequested();
    
        // Notify admin of new leave request submission
        $adminUsers = User::where('role_id', '1')->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new LeaveRequestSubmitted($request->user(), $leaveRequest)); // Pass both user and leave request
        }
    
        // Check if the user is an admin
        if ($request->user()->role_id == '1') {
            return redirect()->route('admin.leave-requests.show', $leaveRequest->id)->with('success', 'Demande de congé soumise avec succès.');
        } else {
            return redirect()->route('leave-requests.index')->with('success', 'Demande de congé soumise avec succès.');
        }
    }

public function update(Request $request, LeaveRequest $leaveRequest)
{
    if ($leaveRequest->processed_count >= 3) {
        return redirect()->back()->with('error', 'Cette demande ne peut pas être traitée plus de trois fois.');
    }

    $request->validate([
        'status' => 'required|in:en attente,approuvé,refusé'
    ]);

    if ($request->status == 'refusé') {
        $leaveRequest->note = null;

        if ($leaveRequest->original_end_date) {
            $leaveRequest->end_date = $leaveRequest->original_end_date;
        }
    }

    $leaveRequest->update($request->only('status'));
    $leaveRequest->processed = true;
    $leaveRequest->processed_count += 1;
    $leaveRequest->save();

    if ($request->status == 'approuvé') {
        $leaveRequest->markAsDeducted();
    }

    if ($leaveRequest->processed_count == 1) {
        $leaveRequest->user->notify(new LeaveRequestProcessed($leaveRequest));
    }

    return redirect()->back()->with('success', 'Statut de la demande mis à jour.');
}


public function show(LeaveRequest $leaveRequest)
{
    $leaveRequest->load('user');
    return Inertia::render('LeaveRequests/LeaveRequestDetails', [
        'leaveRequest' => $leaveRequest
    ]);
}
// app/Http/Controllers/LeaveRequestController.php

public function calendar()
{
    $approvedRequests = LeaveRequest::with('user')
        ->where('status', 'approuvé')
        ->get(['id', 'user_id', 'start_date', 'end_date']);

    return Inertia::render('Admin/LeaveRequests/LeaveRequestsCalendar', [
        'approvedRequests' => $approvedRequests
    ]);
}



}

