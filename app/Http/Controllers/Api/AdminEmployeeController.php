<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminEmployee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;                 // 👈 NEW
use App\Mail\AdminEmployeeQRAssigned;               // 👈 NEW
use App\Models\VisitorScan;

class AdminEmployeeController extends Controller
{
    protected function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = strtolower($user->role->name ?? '');
        if (!in_array($role, ['admin', 'subadmin'], true)) {
            abort(403, 'Only Admin/SubAdmin can manage admin employees.');
        }
    }

    public function index(Request $request): JsonResponse
    {
        $this->ensureAdmin();

        $q = AdminEmployee::query();

        if ($search = $request->query('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $employees = $q->orderByDesc('id')->paginate((int)($request->query('per_page', 15)));
        return response()->json($employees);
    }

    public function show($id): JsonResponse
    {
        $this->ensureAdmin();
        $emp = AdminEmployee::findOrFail($id);
        return response()->json($emp);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureAdmin();

        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'nullable|email|max:255',
                'gender'       => 'nullable|in:male,female,other',
                'dob'          => 'nullable|date',
                'phone_number' => 'nullable|string|max:30',
                'company'      => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json(['message' => $messages, 'errors' => $e->errors()], 422);
        }

        $validated['company'] = $request->input('company', 'LafeLeb');

        $emp = AdminEmployee::create($validated);

        // توليد الـ QR واسترجاع اسم الملف
        $filename = $this->generateQr($emp);

        // إرسال الإيميل مع المرفق إذا الإيميل موجود
        if (!empty($emp->email)) {
            Mail::to($emp->email)->send(new AdminEmployeeQRAssigned($emp, $filename));
        }

        return response()->json([
            'message'  => 'Employee created successfully.',
            'employee' => $emp->fresh(),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $this->ensureAdmin();

        $emp = AdminEmployee::findOrFail($id);

        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'nullable|email|max:255',
                'gender'       => 'nullable|in:male,female,other',
                'dob'          => 'nullable|date',
                'phone_number' => 'nullable|string|max:30',
                'company'      => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode("\n");
            return response()->json(['message' => $messages, 'errors' => $e->errors()], 422);
        }

        if (!array_key_exists('company', $validated)) {
            unset($validated['company']);
        }

        $emp->update($validated);

        // إعادة توليد QR (اختياري ترسل إيميل هون إذا بدك)
        $this->generateQr($emp);

        return response()->json([
            'message'  => 'Employee updated successfully.',
            'employee' => $emp->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->ensureAdmin();

        $emp = AdminEmployee::findOrFail($id);

        if ($emp->qr_code && str_starts_with($emp->qr_code, 'storage/')) {
            $rel = substr($emp->qr_code, strlen('storage/'));
            Storage::disk('public')->delete($rel);
        }

        $emp->delete();

        return response()->json(['message' => 'Employee deleted successfully.']);
    }

    public function check(Request $request): JsonResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $emp = AdminEmployee::find($validated['employee_id']);

        if ($emp) {
            // Log the scan for visitor counting
            VisitorScan::create([
                'scanner_id' => Auth::id(),
                'scanner_type' => 'admin',
                'employee_id' => $emp->id,
                'employee_type' => 'admin_employee',
                'scanner_name' => Auth::user()->name,
                'scanner_company' => 'Admin',
                'employee_name' => $emp->name,
                'employee_company' => $emp->company ?? 'LafeLeb',
                'scan_time' => now(),
            ]);
        }

        return response()->json([
            'exists'   => (bool) $emp,
            'employee' => $emp ?: null,
        ]);
    }

    /**
     * يولّد QR ويخزّنه على قرص public ويحدّث عمود qr_code
     * @return string اسم الملف داخل قرص 'public' (qr_admin_employees/employee_X.png)
     */
    protected function generateQr(AdminEmployee $emp): string
    {
        $data = [
            'id'           => $emp->id,
            'name'         => $emp->name,
            'email'        => $emp->email,
            'gender'       => $emp->gender,
            'dob'          => optional($emp->dob)->toDateString(),
            'phone_number' => $emp->phone_number,
            'company'      => $emp->company ?? 'LafeLeb',
            'created_at'   => $emp->created_at?->toDateTimeString(),
            'updated_at'   => $emp->updated_at?->toDateTimeString(),
        ];

        $qr = new QrCode(json_encode($data));
        $writer = new PngWriter();
        $result = $writer->write($qr);

        $dir = 'qr_admin_employees';
        $filename = $dir . '/employee_' . $emp->id . '.png';

        Storage::disk('public')->put($filename, $result->getString());

        $emp->qr_code = 'storage/' . $filename;
        $emp->save();

        return $filename; // 👈 نرجّع اسم الملف على قرص public
    }
}
