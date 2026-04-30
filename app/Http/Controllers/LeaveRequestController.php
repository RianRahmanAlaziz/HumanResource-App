<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\Employee;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (session('role')  == 'HR') {
            $leaveRequests = LeaveRequest::all();
        } else {
            $leaveRequests = LeaveRequest::where('employee_id', session('employee_id'))->get();
        }

        return view('dashboard.leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('dashboard.leave_requests.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (session('role') != 'HR') {
            // Kalau bukan HR, maka employee_id diambil dari session.
            $request->merge(['employee_id' => session('employee_id')]);
        }

        // Ketika pertama kali membuat request cuti, statusnya adalah pending
        $request->merge(['status' => 'pending']);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        LeaveRequest::create($request->all());

        return redirect()->route('leave-requests.index')->with('success', 'Leave Request Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('dashboard.leave_requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        $employees = Employee::all();
        $statuses = ['pending', 'confirmed', 'rejected'];

        return view('dashboard.leave_requests.edit', compact('leaveRequest', 'employees', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        $leaveRequest->update($request->all());

        return redirect()->route('leave-requests.index')->with('success', 'Leave Request Updated Successfully');
    }

    public function confirm(int $id)
    {
        LeaveRequest::findOrFail($id)->update([
            'status' => 'confirmed',
        ]);

        // Jika sudah diconfirm, masukan dalam absensi jenis cuti.


        return redirect()->route('leave-requests.index')->with('success', 'Leave Request Updated Successfully');
    }

    public function reject(int $id)
    {
        LeaveRequest::findOrFail($id)->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave Request Updated Successfully');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave Request Deleted Successfully');
    }
}
