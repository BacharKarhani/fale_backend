<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class VisitorScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'scanner_id',
        'scanner_type',
        'employee_id',
        'employee_type',
        'scanner_name',
        'scanner_company',
        'employee_name',
        'employee_company',
        'scan_time',
        'location',
        'notes',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    /**
     * Get the scanner (User) who performed the scan
     */
    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanner_id');
    }

    /**
     * Get admin employee if employee_type is 'admin_employee'
     */
    public function adminEmployee()
    {
        return $this->belongsTo(AdminEmployee::class, 'employee_id')
            ->where('employee_type', 'admin_employee');
    }

    /**
     * Get application employee if employee_type is 'application_employee'
     */
    public function applicationEmployee()
    {
        return $this->belongsTo(ApplicationEmployee::class, 'employee_id')
            ->where('employee_type', 'application_employee');
    }

    /**
     * Scope to get scans for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('scan_time', $date);
    }

    /**
     * Scope to get scans for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scan_time', Carbon::today());
    }

    /**
     * Scope to get scans by scanner type
     */
    public function scopeByScannerType($query, $type)
    {
        return $query->where('scanner_type', $type);
    }

    /**
     * Scope to get scans by employee type
     */
    public function scopeByEmployeeType($query, $type)
    {
        return $query->where('employee_type', $type);
    }

    /**
     * Scope to get scans by company
     */
    public function scopeByCompany($query, $company)
    {
        return $query->where('scanner_company', $company);
    }

    /**
     * Get total visitor count for a specific date
     */
    public static function getDailyCount($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        return self::whereDate('scan_time', $date)->count();
    }

    /**
     * Get visitor count by company for a specific date
     */
    public static function getDailyCountByCompany($company, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        return self::whereDate('scan_time', $date)
            ->where('scanner_company', $company)
            ->count();
    }

    /**
     * Get visitor count by scanner type for a specific date
     */
    public static function getDailyCountByScannerType($scannerType, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        return self::whereDate('scan_time', $date)
            ->where('scanner_type', $scannerType)
            ->count();
    }

    /**
     * Get detailed statistics for a date range
     */
    public static function getStatistics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::today();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::today();

        $query = self::whereBetween('scan_time', [$startDate->startOfDay(), $endDate->endOfDay()]);

        return [
            'total_scans' => $query->count(),
            'by_scanner_type' => $query->selectRaw('scanner_type, COUNT(*) as count')
                ->groupBy('scanner_type')
                ->pluck('count', 'scanner_type'),
            'by_employee_type' => $query->selectRaw('employee_type, COUNT(*) as count')
                ->groupBy('employee_type')
                ->pluck('count', 'employee_type'),
            'by_company' => $query->selectRaw('scanner_company, COUNT(*) as count')
                ->groupBy('scanner_company')
                ->pluck('count', 'scanner_company'),
            'daily_breakdown' => $query->selectRaw('DATE(scan_time) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date'),
        ];
    }
}
