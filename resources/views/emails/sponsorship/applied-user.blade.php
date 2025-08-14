<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sponsorship Application Received</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>
    <p>Thanks for applying for the <strong>{{ $bundle->type }}</strong> sponsorship bundle.</p>
    <p>We’ll review your application and get back to you shortly.</p>

    <h3>Bundle Summary</h3>
    <ul>
        <li><strong>Type:</strong> {{ $bundle->type }}</li>
        <li><strong>Price:</strong> ${{ number_format((float) $bundle->price, 2) }}</li>
        @php
            $benefits = is_array($bundle->benefits) ? $bundle->benefits : json_decode($bundle->benefits ?? '[]', true);
        @endphp
        @if(!empty($benefits))
            <li><strong>Benefits:</strong>
                <ul>
                    @foreach($benefits as $b)
                        <li>{{ $b }}</li>
                    @endforeach
                </ul>
            </li>
        @endif
    </ul>

    <p>Best regards,<br>LAFE Team</p>
</body>
</html>
