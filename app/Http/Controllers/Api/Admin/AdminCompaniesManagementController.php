<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\BoothApplication;
use App\Models\ApplicationEmployee;

class AdminCompaniesManagementController extends Controller
{
    /**
     * 1) List all companies
     */
    public function index(Request $request): JsonResponse
    {
        $companies = User::whereHas('role', function ($q) {
            $q->where('name', 'company');
        })->get();

        return response()->json([
            'users' => $companies
        ]);
    }

    /**
     * 2) Admin: Check if employee exists by ID or QR (NO SLOT)
     */
    public function checkEmployeeAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id'          => 'nullable|integer',
            'booth_application_id' => 'nullable|integer',
            'qr_code'              => 'nullable|string', // raw JSON string
            'payload'              => 'nullable|array',  // decoded JSON as array
        ]);

        // Determine identifiers
        $employeeId        = $validated['employee_id'] ?? null;
        $boothApplicationId= $validated['booth_application_id'] ?? null;
        $payload           = $validated['payload'] ?? null;

        // If QR raw JSON string is sent
        if (!$employeeId && !$boothApplicationId && !empty($validated['qr_code'])) {
            try {
                $json = json_decode($validated['qr_code'], true);
                if (is_array($json)) {
                    $employeeId         = $json['id'] ?? $employeeId;
                    $boothApplicationId = $json['booth_application_id'] ?? $boothApplicationId;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // If payload array is sent
        if ($payload) {
            $employeeId         = $payload['id'] ?? $employeeId;
            $boothApplicationId = $payload['booth_application_id'] ?? $boothApplicationId;
        }

        // Nothing to search with
        if (!$employeeId && !$boothApplicationId) {
            return response()->json([
                'exists'      => false,
                'error'       => 'No identifier provided',
                'employee'    => null,
                'application' => null,
                'area'        => null,
                'company'     => null,
            ]);
        }

        // Find employee (priority: both ids -> by employee id -> by application id)
        $employee = null;

        if ($employeeId && $boothApplicationId) {
            $employee = ApplicationEmployee::where('id', $employeeId)
                ->where('booth_application_id', $boothApplicationId)
                ->first();
        }

        if (!$employee && $employeeId) {
            $employee = ApplicationEmployee::find($employeeId);
        }

        if (!$employee && $boothApplicationId) {
            $employee = ApplicationEmployee::where('booth_application_id', $boothApplicationId)->first();
        }

        // Load application (with area + user) based on booth_application_id
        $application = null;
        $area        = null;
        $company     = null;

        if ($boothApplicationId) {
            $application = BoothApplication::with(['area', 'user'])->find($boothApplicationId);
        } elseif ($employee && $employee->booth_application_id) {
            $application = BoothApplication::with(['area', 'user'])->find($employee->booth_application_id);
        }

        if ($application) {
            $area    = $application->area;
            $company = $application->user;
        }

        if ($employee) {
            return response()->json([
                'exists'      => true,
                'employee'    => $employee,
                'application' => $application,
                'area'        => $area,
                'company'     => $company,
            ]);
        }

        return response()->json([
            'exists'      => false,
            'employee'    => null,
            'application' => $application,
            'area'        => $area,
            'company'     => $company,
        ]);
    }

    /**
     * 3) Admin - Check Booth Application by ID (for QR scan) — NO SLOT
     */
    public function checkBoothApplication($id): JsonResponse
    {
        $application = BoothApplication::with(['user', 'area'])->find($id);

        if (!$application) {
            return response()->json([
                'exists'      => false,
                'application' => null,
                'company'     => null,
                'area'        => null,
            ]);
        }

        return response()->json([
            'exists'      => true,
            'application' => $application,
            'company'     => $application->user,
            'area'        => $application->area,
        ]);
    }

    /**
     * 4) List company booth applications with employees and area info — NO SLOT
     */
    public function employeesWithTickets($id): JsonResponse
    {
        $company = User::findOrFail($id);

        $applications = BoothApplication::with(['area', 'employees'])
            ->where('user_id', $company->id)
            ->get();

        $resultApplications = [];

        foreach ($applications as $application) {
            $area = $application->area;

            $area_details = $area ? [
                'label'         => $area->label,
                'dimensions'    => $area->dimensions,
                'price'         => $area->price,
                'benefits'      => $area->benefits,
                'ticket_number' => $area->ticket_number,
            ] : null;

            $employees = [];
            foreach ($application->employees as $employee) {
                $employees[] = [
                    'name'         => $employee->name,
                    'email'        => $employee->email,
                    'phone_number' => $employee->phone_number,
                    'gender'       => $employee->gender,
                    'dob'          => $employee->dob,
                ];
            }

            $resultApplications[] = [
                'area_details' => $area_details,
                'employees'    => $employees,
            ];
        }

        return response()->json([
            'company' => [
                'id'     => $company->id,
                'name'   => $company->name,
                'email'  => $company->email,
                'phone'  => $company->phone,
                'gender' => $company->gender,
            ],
            'applications' => $resultApplications,
        ]);
    }
}
