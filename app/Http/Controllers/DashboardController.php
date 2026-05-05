<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Presence;

class DashboardController extends Controller
{
    public function index()
    {
        $employees = Employee::count();
        $departments = Department::count();
        $payroll = Payroll::count();
        $presences = Presence::count();
        $tasks = Task::all();

        return view('dashboard.index', compact('tasks', 'employees', 'departments', 'payroll', 'presences'));
    }


    public function presence()
    {
        $data = Presence::where('status', 'present')
            ->selectRaw('MONTH(date) as month, YEAR(date) as year, COUNT(*) as total_present')
            ->groupBy('year', 'month')
            ->orderBy('month', 'asc')
            ->get();

        $temp = [];
        $i = 0;

        foreach ($data as $item) {
            $temp[$i] = $item->total_present;
            $i++;
        }

        return response()->json($temp);
    }
}
