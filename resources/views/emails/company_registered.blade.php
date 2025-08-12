<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Company Registration</title>
</head>
<body>
    <h2>New Company Registration (Pending Approval)</h2>

    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Phone:</strong> {{ $user->phone ?: '-' }}</p>
    <p><strong>Company:</strong> {{ $user->company_name }}</p>

    @php
        $specs = $user->specialization ? json_decode($user->specialization, true) : [];
    @endphp
    <p><strong>Specialization:</strong> {{ !empty($specs) ? implode(', ', $specs) : '-' }}</p>

    <p><strong>Status:</strong> Pending Admin Approval</p>
</body>
</html>
