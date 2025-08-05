<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BoothApplication;

class AdminCompaniesManagementController extends Controller
{
    // 1. List all companies
    public function index(Request $request)
    {
        // All users with role 'company'
        $companies = User::whereHas('role', function ($q) {
            $q->where('name', 'company');
        })->get();

        return response()->json([
            'users' => $companies
        ]);
    }

    // 2. Check if employee exists by ID or QR (ADMIN)
    public function checkEmployeeAdmin(Request $request): \Illuminate\Http\JsonResponse
{
    // Allow full QR payload as JSON
    $validated = $request->validate([
        'employee_id'         => 'nullable|integer',
        'booth_application_id'=> 'nullable|integer',
        'qr_code'             => 'nullable|string',
        'payload'             => 'nullable|array', // Pass the full QR as array if you want
    ]);

    // 1. Determine the actual employee id and booth_application_id
    $employeeId = $validated['employee_id'] ?? null;
    $boothApplicationId = $validated['booth_application_id'] ?? null;
    $payload = $validated['payload'] ?? null;

    // If sent as raw QR JSON string, parse it!
    if (!$employeeId && !$boothApplicationId && $validated['qr_code']) {
        $qrString = $validated['qr_code'];
        $jsonData = null;
        try {
            $jsonData = json_decode($qrString, true);
        } catch (\Throwable $th) {
            // Ignore, not JSON
        }
        if ($jsonData) {
            $employeeId = $jsonData['id'] ?? null;
            $boothApplicationId = $jsonData['booth_application_id'] ?? null;
            // You can read more if needed
        }
    }
    // If payload sent as array
    if ($payload) {
        $employeeId = $payload['id'] ?? $employeeId;
        $boothApplicationId = $payload['booth_application_id'] ?? $boothApplicationId;
    }

    // If nothing found
    if (!$employeeId && !$boothApplicationId) {
        return response()->json([
            'exists' => false,
            'error' => 'No identifier provided',
            'employee' => null,
            'application' => null,
            'area' => null,
            'slot' => null,
            'company' => null,
        ]);
    }

    // 2. Always try to get employee by booth_application_id + id (strongest case)
    $employee = null;
    if ($employeeId && $boothApplicationId) {
        $employee = \App\Models\ApplicationEmployee::where('id', $employeeId)
            ->where('booth_application_id', $boothApplicationId)
            ->first();
    }
    // Or, get employee by ID alone (fallback)
    if (!$employee && $employeeId) {
        $employee = \App\Models\ApplicationEmployee::where('id', $employeeId)->first();
    }
    // Or, by booth_application_id alone (get first employee)
    if (!$employee && $boothApplicationId) {
        $employee = \App\Models\ApplicationEmployee::where('booth_application_id', $boothApplicationId)->first();
    }

    // 3. Get the application and relationships via booth_application_id
    $application = null;
    $area = null;
    $slot = null;
    $company = null;

    if ($boothApplicationId) {
        $application = \App\Models\BoothApplication::with(['area', 'slot', 'user'])
            ->find($boothApplicationId);
        if ($application) {
            $area = $application->area;
            $slot = $application->slot;
            $company = $application->user;
        }
    } elseif ($employee && $employee->booth_application_id) {
        $application = \App\Models\BoothApplication::with(['area', 'slot', 'user'])
            ->find($employee->booth_application_id);
        if ($application) {
            $area = $application->area;
            $slot = $application->slot;
            $company = $application->user;
        }
    }

    // 4. Return result
    if ($employee) {
        return response()->json([
            'exists'      => true,
            'employee'    => $employee,
            'application' => $application,
            'area'        => $area,
            'slot'        => $slot,
            'company'     => $company,
        ]);
    } else {
        return response()->json([
            'exists'      => false,
            'employee'    => null,
            'application' => $application,
            'area'        => $area,
            'slot'        => $slot,
            'company'     => $company,
        ]);
    }
}


    // 3. Admin - Check Booth Application by ID (for QR scan)
    public function checkBoothApplication($id)
    {
        // Eager-load related models
        $application = \App\Models\BoothApplication::with(['user', 'area', 'slot'])->find($id);

        if (!$application) {
            return response()->json([
                'exists' => false,
                'application' => null,
                'company' => null,
                'area' => null,
                'slot' => null,
            ]);
        }

        return response()->json([
            'exists' => true,
            'application' => $application,
            'company' => $application->user,
            'area' => $application->area,
            'slot' => $application->slot,
        ]);
    }

    // 4. List company booth applications with employees and area/slot info
    public function employeesWithTickets($id)
    {
        // Get the company (with phone/gender/other columns)
        $company = \App\Models\User::findOrFail($id);

        // Get all booth applications for this company, with area, slot, and employees
        $applications = \App\Models\BoothApplication::with(['area', 'slot', 'employees'])
            ->where('user_id', $company->id)
            ->get();

        $resultApplications = [];
        foreach ($applications as $application) {
            // Area details
            $area = $application->area;
            $area_details = $area ? [
                'label' => $area->label,
                'dimensions' => $area->dimensions,
                'price' => $area->price,
                'benefits' => $area->benefits,
                'ticket_number' => $area->ticket_number,
            ] : null;

            // Slot details
            $slot = $application->slot;
            $slot_details = $slot ? [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
            ] : null;

            // Employees for this application
            $employees = [];
            foreach ($application->employees as $employee) {
                $employees[] = [
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone_number' => $employee->phone_number,
                    'gender' => $employee->gender,
                    'dob' => $employee->dob,
                ];
            }

            $resultApplications[] = [
                'area_details' => $area_details,
                'slot_details' => $slot_details,
                'employees' => $employees,
            ];
        }

        return response()->json([
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'gender' => $company->gender,
            ],
            'applications' => $resultApplications,
        ]);
    }

}
