<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitorScan;
use App\Models\AdminEmployee;
use App\Models\ApplicationEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class VisitorScanController extends Controller
{
    /**
     * Scan a QR code and log the visitor count
     * This method can be used by both admins and companies
     */
    public function scanQr(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'employee_type' => 'required|string|in:admin_employee,application_employee',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json([
                'message' => $messages,
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Auth::user();
        $employeeId = $validated['employee_id'];
        $employeeType = $validated['employee_type'];

        // Determine scanner type based on user role
        $scannerType = $user->isAdmin() ? 'admin' : 'company';
        $scannerCompany = $user->isAdmin() ? 'Admin' : $user->company_name;

        // Find the employee
        $employee = null;
        $employeeName = '';
        $employeeCompany = '';

        if ($employeeType === 'admin_employee') {
            $employee = AdminEmployee::find($employeeId);
            if ($employee) {
                $employeeName = $employee->name;
                $employeeCompany = $employee->company ?? 'LafeLeb';
            }
        } elseif ($employeeType === 'application_employee') {
            $employee = ApplicationEmployee::find($employeeId);
            if ($employee) {
                $employeeName = $employee->name;
                // Get company name from the booth application
                $employeeCompany = $employee->application->user->company_name ?? 'Unknown';
            }
        }

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found.',
                'exists' => false,
            ], 404);
        }

        // Log the scan
        $scan = VisitorScan::create([
            'scanner_id' => $user->id,
            'scanner_type' => $scannerType,
            'employee_id' => $employeeId,
            'employee_type' => $employeeType,
            'scanner_name' => $user->name,
            'scanner_company' => $scannerCompany,
            'employee_name' => $employeeName,
            'employee_company' => $employeeCompany,
            'scan_time' => now(),
            'location' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'QR code scanned successfully.',
            'exists' => true,
            'scan_id' => $scan->id,
            'employee' => [
                'id' => $employee->id,
                'name' => $employeeName,
                'company' => $employeeCompany,
                'type' => $employeeType,
            ],
            'scanner' => [
                'id' => $user->id,
                'name' => $user->name,
                'company' => $scannerCompany,
                'type' => $scannerType,
            ],
            'scan_time' => $scan->scan_time,
        ]);
    }

    /**
     * Get daily visitor count for admins
     */
    public function getDailyCount(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only admins can access this endpoint
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        
        try {
            $parsedDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format.'], 422);
        }

        $totalCount = VisitorScan::getDailyCount($parsedDate);
        $adminScans = VisitorScan::getDailyCountByScannerType('admin', $parsedDate);
        $companyScans = VisitorScan::getDailyCountByScannerType('company', $parsedDate);

        return response()->json([
            'date' => $parsedDate->toDateString(),
            'total_visitors' => $totalCount,
            'admin_scans' => $adminScans,
            'company_scans' => $companyScans,
            'breakdown' => [
                'by_scanner_type' => [
                    'admin' => $adminScans,
                    'company' => $companyScans,
                ],
                'by_employee_type' => VisitorScan::forDate($parsedDate)
                    ->selectRaw('employee_type, COUNT(*) as count')
                    ->groupBy('employee_type')
                    ->pluck('count', 'employee_type'),
                'by_company' => VisitorScan::forDate($parsedDate)
                    ->selectRaw('scanner_company, COUNT(*) as count')
                    ->groupBy('scanner_company')
                    ->pluck('count', 'scanner_company'),
            ],
        ]);
    }

    /**
     * Get simple daily visitor count for admins (count only)
     */
    public function getDailyCountOnly(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only admins can access this endpoint
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        
        try {
            $parsedDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format.'], 422);
        }

        $totalCount = VisitorScan::getDailyCount($parsedDate);

        return response()->json([
            'date' => $parsedDate->toDateString(),
            'visitor_count' => $totalCount,
        ]);
    }

    /**
     * Get simple visitor count for a specific company (count only)
     */
    public function getCompanyDailyCountOnly(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only company users can access this endpoint
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        
        try {
            $parsedDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format.'], 422);
        }

        $companyCount = VisitorScan::getDailyCountByCompany($user->company_name, $parsedDate);

        return response()->json([
            'date' => $parsedDate->toDateString(),
            'company' => $user->company_name,
            'visitor_count' => $companyCount,
        ]);
    }
    public function getCompanyDailyCount(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only company users can access this endpoint
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        
        try {
            $parsedDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format.'], 422);
        }

        $companyCount = VisitorScan::getDailyCountByCompany($user->company_name, $parsedDate);

        return response()->json([
            'date' => $parsedDate->toDateString(),
            'company' => $user->company_name,
            'visitor_count' => $companyCount,
            'scans_by_employee_type' => VisitorScan::forDate($parsedDate)
                ->byCompany($user->company_name)
                ->selectRaw('employee_type, COUNT(*) as count')
                ->groupBy('employee_type')
                ->pluck('count', 'employee_type'),
        ]);
    }

    /**
     * Get detailed statistics for admins
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only admins can access this endpoint
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $startDate = $request->query('start_date', Carbon::today()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        
        try {
            $parsedStartDate = Carbon::parse($startDate);
            $parsedEndDate = Carbon::parse($endDate);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format.'], 422);
        }

        $statistics = VisitorScan::getStatistics($parsedStartDate, $parsedEndDate);

        return response()->json([
            'period' => [
                'start_date' => $parsedStartDate->toDateString(),
                'end_date' => $parsedEndDate->toDateString(),
            ],
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get recent scans for admins
     */
    public function getRecentScans(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only admins can access this endpoint
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $limit = $request->query('limit', 50);
        $scans = VisitorScan::with(['scanner'])
            ->orderBy('scan_time', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'scans' => $scans,
            'total_count' => $scans->count(),
        ]);
    }

    /**
     * Get recent scans for a specific company
     */
    public function getCompanyRecentScans(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only company users can access this endpoint
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $limit = $request->query('limit', 50);
        $scans = VisitorScan::with(['scanner'])
            ->byCompany($user->company_name)
            ->orderBy('scan_time', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'company' => $user->company_name,
            'scans' => $scans,
            'total_count' => $scans->count(),
        ]);
    }

    /**
     * Check if an employee exists (without logging a scan)
     * This is useful for validation before scanning
     */
    public function checkEmployee(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'employee_type' => 'required|string|in:admin_employee,application_employee',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json([
                'message' => $messages,
                'errors' => $e->errors(),
            ], 422);
        }

        $employeeId = $validated['employee_id'];
        $employeeType = $validated['employee_type'];

        $employee = null;
        $employeeName = '';
        $employeeCompany = '';

        if ($employeeType === 'admin_employee') {
            $employee = AdminEmployee::find($employeeId);
            if ($employee) {
                $employeeName = $employee->name;
                $employeeCompany = $employee->company ?? 'LafeLeb';
            }
        } elseif ($employeeType === 'application_employee') {
            $employee = ApplicationEmployee::find($employeeId);
            if ($employee) {
                $employeeName = $employee->name;
                $employeeCompany = $employee->application->user->company_name ?? 'Unknown';
            }
        }

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found.',
                'exists' => false,
            ], 404);
        }

        return response()->json([
            'message' => 'Employee found.',
            'exists' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employeeName,
                'company' => $employeeCompany,
                'type' => $employeeType,
            ],
        ]);
    }
}