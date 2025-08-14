<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Sponsorship Application</title>
</head>
<body>
    <h2>New Sponsorship Application</h2>
    <p><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>

    <h3>Bundle</h3>
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
</body>
</html>
