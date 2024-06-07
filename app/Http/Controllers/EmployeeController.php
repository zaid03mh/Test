<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('created_at', 'desc')->paginate(10); // Fetches 10 employees per page, ordered by most recent
        return Inertia::render('Admin/employes/EmployeesList', ['employees' => $employees]);
    }

    public function create()
    {
        return Inertia::render('Admin/employes/EmployeeCreate');
    }

    // Store a new employee
    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'birthdate' => 'required|date|before:' . now()->subYears(16)->toDateString(),
        'hire_date' => 'required|date',
        'email' => 'required|string|email|max:255|unique:employees',
        'phone_number' => 'nullable|string|size:10',
    ], [
        'birthdate.before' => 'L\'âge ne doit pas être inférieur à 16 ans.',
        'email.email' => 'L\'email doit avoir le format d\'un email.',
        'phone_number.size' => 'Le numéro de téléphone ne doit pas dépasser 10 chiffres.',
    ]);

    // Create the employee
    $employee = Employee::create($validated);

    // Create a corresponding user
    $user = User::create([
        'name' => $employee->first_name . ' ' . $employee->last_name,
        'email' => $employee->email,
        'password' => Hash::make('12345678'), // Default password
        'role_id' => 2, // Use a configuration value instead of a hardcoded value
    ]);

    return redirect()->route('employees.index')->with('success', 'Employé créé avec succès');
}


    // Show a single employee
    public function show(Employee $employee)
    {
        $annualLeaveByYear = $employee->calculateLeavePerYear();

        // Get the current year
        $currentYear = now()->year;

        // Filter the array to include only the last 5 years
        $filteredAnnualLeaveByYear = collect($annualLeaveByYear)
            ->filter(function ($value, $year) use ($currentYear) {
                return $year >= ($currentYear - 4);
            });

        return Inertia::render('Admin/employes/EmployeeDetail', [
            'employee' => $employee,
            'annualLeaveByYear' => $filteredAnnualLeaveByYear
        ]);
    }

    public function edit(Employee $employee)
    {
        return Inertia::render('Admin/employes/EmployeeEdit', ['employee' => $employee]);
    }

    // Update an existing employee
    public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'birthdate' => 'required|date|before:' . now()->subYears(16)->toDateString(),
        'hire_date' => 'required|date',
        'email' => 'required|string|email|max:255|unique:employees,email,' . $employee->id,
        'phone_number' => 'nullable|string|size:10',
    ], [
        'birthdate.before' => 'L\'âge ne doit pas être inférieur à 16 ans.',
        'email.email' => 'L\'email doit avoir le format d\'un email.',
        'phone_number.size' => 'Le numéro de téléphone ne doit pas dépasser 10 chiffres.',
    ]);

    $employee->update($validated);

    return redirect()->route('employees.index')->with('success', 'Employé mis à jour avec succès');
}
    // Delete an employee
    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            // Retrieve the associated user
            $user = User::where('email', $employee->email)->first();
            
            // Delete the employee
            $employee->delete();

            // Delete the associated user if exists
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('employees.index')->with('success', 'Employé Supprimé avec succés');
    }
    public function dashboard()
{
    // Fetch the authenticated user
    $user = Auth::user();

    // Fetch the corresponding employee details
    $employee = Employee::where('email', $user->email)->firstOrFail();

    // Fetch the annual leave data
    $annualLeaveByYear = $employee->calculateLeavePerYear();

    // Get the current year
    $currentYear = now()->year;

    // Filter the array to include only the last 5 years
    $filteredAnnualLeaveByYear = collect($annualLeaveByYear)
        ->filter(function ($value, $year) use ($currentYear) {
            return $year >= ($currentYear - 4);
        });

    // Return the data with Inertia
    return Inertia::render('User/Dashboard', [
        'employee' => $employee,
        'annualLeaveByYear' => $filteredAnnualLeaveByYear
    ]);
}

    
}
