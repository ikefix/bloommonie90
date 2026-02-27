<h2>Inventory Complaint Submitted</h2>

<p><strong>Name:</strong> {{ $complaint->user->name }}</p>
<p><strong>Email:</strong> {{ $complaint->user->email }}</p>
<p><strong>Role:</strong> {{ $complaint->user->role }}</p>

<hr>

<p><strong>Message:</strong></p>
<p>{{ $complaint->message }}</p>

@if($complaint->image)
    <p><strong>Attached Image:</strong></p>
    <img src="{{ asset('storage/'.$complaint->image) }}" width="300">
@endif