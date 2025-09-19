<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoothApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeQRAssigned;
use App\Mail\EmployeeQRUpdated;
use App\Mail\EmployeeQRRemoved;
use App\Models\VisitorScan;

class CompanyDashboardController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        $applications = $user->boothApplications()
            ->with(['area']) // ⬅️ أزلنا slot
            ->get()
            ->map(function ($app) {
                $app->ticket_number = $app->area->ticket_number ?? null;
                return $app;
            });

        return response()->json([
            'user' => $user,
            'applications' => $applications,
        ]);
    }

    public function applicationDetails($id): JsonResponse
    {
        $user = Auth::user();

        $application = BoothApplication::with(['area', 'employees']) // ⬅️ أزلنا slot
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $maxTickets = $application->area->ticket_number ?? 0;
        $createdTickets = $application->employees->count();

        return response()->json([
            'application'     => $application,
            'max_tickets'     => $maxTickets,
            'created_tickets' => $createdTickets,
            'employees'       => $application->employees,
        ]);
    }

    public function assignEmployee(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $application = BoothApplication::with(['area', 'employees']) // ⬅️ أزلنا slot
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $maxTickets = $application->area->ticket_number ?? 0;
        $createdTickets = $application->employees->count();

        if ($createdTickets >= $maxTickets) {
            return response()->json(['message' => 'No more tickets available.'], 400);
        }

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'nullable|email|max:255',
                'gender'        => 'nullable|string|in:male,female,other',
                'dob'           => 'nullable|date',
                'phone_number'  => 'nullable|string|max:30',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json([
                'message' => $messages,
                'errors'  => $e->errors(),
            ], 422);
        }

        $employee = $application->employees()->create($validated);

        // Generate QR
        $data = [
            'id'                   => $employee->id,
            'booth_application_id' => $employee->booth_application_id,
            'name'                 => $employee->name,
            'email'                => $employee->email,
            'gender'               => $employee->gender,
            'dob'                  => $employee->dob,
            'phone_number'         => $employee->phone_number,
            'created_at'           => $employee->created_at->toDateTimeString(),
            'updated_at'           => $employee->updated_at->toDateTimeString(),
        ];

        $jsonData = json_encode($data);
        $qr = new QrCode($jsonData);
        $writer = new PngWriter();
        $result = $writer->write($qr);

        $filename = 'qr_employees/employee_' . $employee->id . '.png';
        Storage::disk('public')->put($filename, $result->getString());

        $employee->qr_code = 'storage/' . $filename;
        $employee->save();

        if (!empty($employee->email)) {
            Mail::to($employee->email)->send(
                new EmployeeQRAssigned($employee, $application, $filename)
            );
        }

        return response()->json([
            'message'  => 'Employee assigned successfully.',
            'employee' => $employee
        ]);
    }

    public function updateEmployee(Request $request, $applicationId, $employeeId): JsonResponse
    {
        $user = Auth::user();

        $application = BoothApplication::with(['area', 'employees']) // ⬅️ أزلنا slot
            ->where('user_id', $user->id)
            ->findOrFail($applicationId);

        $employee = $application->employees()->findOrFail($employeeId);

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'nullable|email|max:255',
                'gender'        => 'nullable|string|in:male,female,other',
                'dob'           => 'nullable|date',
                'phone_number'  => 'nullable|string|max:30',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json([
                'message' => $messages,
                'errors'  => $e->errors(),
            ], 422);
        }

        $employee->update($validated);

        // Regenerate QR
        $data = [
            'id'                   => $employee->id,
            'booth_application_id' => $employee->booth_application_id,
            'name'                 => $employee->name,
            'email'                => $employee->email,
            'gender'               => $employee->gender,
            'dob'                  => $employee->dob,
            'phone_number'         => $employee->phone_number,
            'created_at'           => $employee->created_at->toDateTimeString(),
            'updated_at'           => $employee->updated_at->toDateTimeString(),
        ];

        $jsonData = json_encode($data);
        $qr = new QrCode($jsonData);
        $writer = new PngWriter();
        $result = $writer->write($qr);

        $filename = 'qr_employees/employee_' . $employee->id . '.png';
        Storage::disk('public')->put($filename, $result->getString());

        $employee->qr_code = 'storage/' . $filename;
        $employee->save();

        if (!empty($employee->email)) {
            Mail::to($employee->email)->send(
                new EmployeeQRUpdated($employee, $application, $filename)
            );
        }

        return response()->json([
            'message'  => 'Employee updated successfully.',
            'employee' => $employee
        ]);
    }

    public function removeEmployee($applicationId, $employeeId): JsonResponse
    {
        $user = Auth::user();

        $application = BoothApplication::with(['area', 'employees']) // ⬅️ أزلنا slot
            ->where('user_id', $user->id)
            ->findOrFail($applicationId);

        $employee = $application->employees()->findOrFail($employeeId);
        $email = $employee->email;
        $name  = $employee->name;
        $applicationData = $application;

        $employee->delete();

        if (!empty($email)) {
            Mail::to($email)->send(
                new EmployeeQRRemoved($name, $applicationData)
            );
        }

        return response()->json([
            'message' => 'Employee removed successfully.'
        ]);
    }

    public function checkEmployee(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $validated['employee_id'];

        // Applications for this company with employees + area + user (no slot)
        $applications = BoothApplication::with(['employees', 'area', 'user'])
            ->where('user_id', $user->id)
            ->get();

        $employeeData = null;
        $companyData  = null;
        $areaData     = null;

        foreach ($applications as $app) {
            foreach ($app->employees as $emp) {
                if ((int)$emp->id === (int)$employeeId) {
                    $employeeData = $emp;
                    $companyData  = $app->user;
                    $areaData     = $app->area;
                    break 2;
                }
            }
        }

        if ($employeeData) {
            // Log the scan for visitor counting
            VisitorScan::create([
                'scanner_id' => $user->id,
                'scanner_type' => 'company',
                'employee_id' => $employeeData->id,
                'employee_type' => 'application_employee',
                'scanner_name' => $user->name,
                'scanner_company' => $user->company_name,
                'employee_name' => $employeeData->name,
                'employee_company' => $companyData->company_name,
                'scan_time' => now(),
            ]);

            return response()->json([
                'exists'   => true,
                'employee' => $employeeData,
                'company'  => $companyData,
                'area'     => $areaData,
            ]);
        }

        return response()->json([
            'exists'   => false,
            'employee' => null,
            'company'  => null,
            'area'     => null,
        ]);
    }
}
