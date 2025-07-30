<h2>New Bundle Application</h2>
<p>User <strong>{{ $user->name }}</strong> ({{ $user->email }}) applied for:</p>
<ul>
    <li>Bundle Type: {{ $bundle->type }}</li>
    <li>Price: ${{ $bundle->price }}</li>
</ul>
