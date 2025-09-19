# Visitor Counting System Documentation

## Overview
The visitor counting system allows admins and companies to scan QR codes and track visitor counts throughout the day. The system automatically logs every QR scan and provides detailed statistics for admins.

## Features

### 1. Automatic Scan Logging
- Every QR code scan is automatically logged in the `visitor_scans` table
- Tracks who scanned (admin or company), what was scanned (employee), and when
- Includes optional location and notes for each scan

### 2. Daily Visitor Counts
- Admins can view total visitor counts for any date
- Companies can view their own visitor counts for any date
- Breakdown by scanner type (admin vs company) and employee type

### 3. Detailed Statistics
- Comprehensive statistics for date ranges
- Breakdown by company, scanner type, and employee type
- Daily breakdown charts for trend analysis

## Database Structure

### visitor_scans Table
```sql
- id: Primary key
- scanner_id: ID of the user who scanned (admin or company)
- scanner_type: 'admin' or 'company'
- employee_id: ID of the employee whose QR was scanned
- employee_type: 'admin_employee' or 'application_employee'
- scanner_name: Name of the person who scanned
- scanner_company: Company of the scanner
- employee_name: Name of the scanned employee
- employee_company: Company of the scanned employee
- scan_time: When the scan happened
- location: Optional location where scan happened
- notes: Optional notes about the scan
- created_at, updated_at: Timestamps
```

## API Endpoints

### For Companies (Authenticated with company role)

#### 1. Scan QR Code
```
POST /api/company/scan-qr
```
**Request Body:**
```json
{
    "employee_id": 123,
    "employee_type": "application_employee",
    "location": "Main Entrance",
    "notes": "Optional notes"
}
```
**Response:**
```json
{
    "message": "QR code scanned successfully.",
    "exists": true,
    "scan_id": 456,
    "employee": {
        "id": 123,
        "name": "John Doe",
        "company": "ABC Corp",
        "type": "application_employee"
    },
    "scanner": {
        "id": 789,
        "name": "Jane Smith",
        "company": "XYZ Ltd",
        "type": "company"
    },
    "scan_time": "2025-09-19T16:48:00.000000Z"
}
```

#### 2. Get Company Daily Visitor Count
```
GET /api/company/visitor-count?date=2025-09-19
```
**Response:**
```json
{
    "date": "2025-09-19",
    "company": "XYZ Ltd",
    "visitor_count": 15,
    "scans_by_employee_type": {
        "application_employee": 12,
        "admin_employee": 3
    }
}
```

#### 3. Get Company Recent Scans
```
GET /api/company/recent-scans?limit=20
```
**Response:**
```json
{
    "company": "XYZ Ltd",
    "scans": [
        {
            "id": 456,
            "scanner_id": 789,
            "scanner_type": "company",
            "employee_id": 123,
            "employee_type": "application_employee",
            "scanner_name": "Jane Smith",
            "scanner_company": "XYZ Ltd",
            "employee_name": "John Doe",
            "employee_company": "ABC Corp",
            "scan_time": "2025-09-19T16:48:00.000000Z",
            "location": "Main Entrance",
            "notes": "Optional notes"
        }
    ],
    "total_count": 1
}
```

#### 4. Check Employee (without logging scan)
```
POST /api/company/check-employee-qr
```
**Request Body:**
```json
{
    "employee_id": 123,
    "employee_type": "application_employee"
}
```

### For Admins (Authenticated with admin role)

#### 1. Scan QR Code
```
POST /api/admin/scan-qr
```
Same request/response format as company scan, but scanner_type will be "admin"

#### 2. Get Daily Visitor Count (All Companies)
```
GET /api/admin/visitor-count?date=2025-09-19
```
**Response:**
```json
{
    "date": "2025-09-19",
    "total_visitors": 150,
    "admin_scans": 25,
    "company_scans": 125,
    "breakdown": {
        "by_scanner_type": {
            "admin": 25,
            "company": 125
        },
        "by_employee_type": {
            "application_employee": 120,
            "admin_employee": 30
        },
        "by_company": {
            "ABC Corp": 45,
            "XYZ Ltd": 35,
            "Admin": 25,
            "DEF Inc": 45
        }
    }
}
```

#### 3. Get Detailed Statistics
```
GET /api/admin/visitor-statistics?start_date=2025-09-15&end_date=2025-09-19
```
**Response:**
```json
{
    "period": {
        "start_date": "2025-09-15",
        "end_date": "2025-09-19"
    },
    "statistics": {
        "total_scans": 750,
        "by_scanner_type": {
            "admin": 150,
            "company": 600
        },
        "by_employee_type": {
            "application_employee": 600,
            "admin_employee": 150
        },
        "by_company": {
            "ABC Corp": 200,
            "XYZ Ltd": 150,
            "Admin": 150,
            "DEF Inc": 250
        },
        "daily_breakdown": {
            "2025-09-15": 120,
            "2025-09-16": 135,
            "2025-09-17": 150,
            "2025-09-18": 165,
            "2025-09-19": 180
        }
    }
}
```

#### 4. Get Recent Scans (All Companies)
```
GET /api/admin/recent-scans?limit=50
```

#### 5. Check Employee (without logging scan)
```
POST /api/admin/check-employee-qr
```

## Existing QR Check Methods (Updated)

The following existing endpoints now automatically log scans:

### Company Dashboard
- `POST /api/company/check-employee` - Now logs scans when employees are found

### Admin Employee Management
- `POST /api/admin-employees/check` - Now logs scans when admin employees are found

### Admin Companies Management
- `POST /api/admin/check-employee` - Now logs scans when application employees are found

## Usage Examples

### Frontend Integration

#### 1. Company QR Scanner
```javascript
// Scan QR code and log visitor count
async function scanQRCode(employeeId, employeeType, location = null, notes = null) {
    try {
        const response = await fetch('/api/company/scan-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                employee_id: employeeId,
                employee_type: employeeType,
                location: location,
                notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.exists) {
            console.log(`Visitor logged: ${data.employee.name} from ${data.employee.company}`);
            // Update UI with success message
        } else {
            console.log('Employee not found');
            // Show error message
        }
    } catch (error) {
        console.error('Scan failed:', error);
    }
}
```

#### 2. Admin Dashboard - Daily Count
```javascript
// Get daily visitor count
async function getDailyVisitorCount(date = null) {
    try {
        const url = date ? `/api/admin/visitor-count?date=${date}` : '/api/admin/visitor-count';
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const data = await response.json();
        
        console.log(`Total visitors today: ${data.total_visitors}`);
        console.log(`Admin scans: ${data.admin_scans}`);
        console.log(`Company scans: ${data.company_scans}`);
        
        // Update dashboard UI
        updateDashboard(data);
    } catch (error) {
        console.error('Failed to get visitor count:', error);
    }
}
```

#### 3. Company Dashboard - Own Count
```javascript
// Get company's daily visitor count
async function getCompanyVisitorCount(date = null) {
    try {
        const url = date ? `/api/company/visitor-count?date=${date}` : '/api/company/visitor-count';
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const data = await response.json();
        
        console.log(`${data.company} visitors today: ${data.visitor_count}`);
        
        // Update company dashboard
        updateCompanyDashboard(data);
    } catch (error) {
        console.error('Failed to get company visitor count:', error);
    }
}
```

## Security Considerations

1. **Authentication Required**: All endpoints require valid authentication tokens
2. **Role-based Access**: Companies can only access their own data, admins can access all data
3. **Input Validation**: All inputs are validated before processing
4. **Rate Limiting**: Consider implementing rate limiting for scan endpoints to prevent abuse

## Performance Considerations

1. **Database Indexes**: The migration includes indexes on frequently queried columns
2. **Pagination**: Recent scans endpoints support pagination via `limit` parameter
3. **Caching**: Consider caching daily counts for better performance
4. **Batch Operations**: For high-volume scenarios, consider batch insert operations

## Monitoring and Analytics

The system provides comprehensive data for:
- Daily visitor trends
- Company performance comparison
- Peak scanning times
- Employee type distribution
- Scanner type analysis (admin vs company)

This data can be used for:
- Event planning and capacity management
- Company engagement analysis
- Security monitoring
- Performance optimization
