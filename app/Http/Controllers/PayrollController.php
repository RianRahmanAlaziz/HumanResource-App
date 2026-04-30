<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payrolls = Payroll::all();

        return view('dashboard.payrolls.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all(); // Get all employees to associate with payroll
        return view('dashboard.payrolls.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'pay_date' => 'required|date',
        ]);

        Payroll::create($request->all());

        return redirect()->route('payrolls.index')->with('success', 'Payroll record created successfully.');
    }

    // Display the specified slip
    public function show($id)
    {
        $payroll = Payroll::findOrFail($id);
        return view('dashboard.payrolls.show', compact('payroll'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, LeaveRequest $leaveRequest)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = Employee::all();

        return view('dashboard.payrolls.edit', compact('leaveRequest', 'payroll', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'pay_date' => 'required|date',
        ]);

        $payroll = Payroll::findOrFail($id);
        $payroll->update($request->all());

        return redirect()->route('payrolls.index')->with('success', 'Payroll record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Payroll record deleted successfully.');
    }
}
