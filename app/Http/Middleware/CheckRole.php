<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $employeeID = auth()->user()->employee_id;

        $employee = Employee::find($employeeID);

        if (!$employee) {
            abort(404, 'Employee not found');
        }

        if (!$employee->role) {
            abort(403, 'Role not assigned');
        }

        $roleTitle = $employee->role->title;

        // Simpan ke session
        $request->session()->put('role', $roleTitle);
        $request->session()->put('employee_id', $employee->id);

        if (!in_array($roleTitle, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
