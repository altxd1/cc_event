<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - EventPro</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
<div class="container" style="margin-top: 2rem;">
    <h2>Event Details - {{ $event->event_name }}</h2>

    <div class="event-form-grid">
        <div class="form-section">
            <h4>Event Info</h4>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</p>
            <p><strong>Guests:</strong> {{ $event->number_of_guests }}</p>
            <p><strong>Status:</strong> {{ ucfirst($event->status ?? 'pending') }}</p>
        </div>

        <div class="form-section">
            <h4>Venue</h4>
            <p><strong>Place:</strong> {{ $event->place_name }}</p>
            <p><strong>Price:</strong> ${{ number_format((float)$event->place_price, 2) }}</p>
        </div>

        <div class="form-section">
            <h4>Food</h4>
            <p><strong>Menu:</strong> {{ $event->food_name }}</p>
            <p><strong>Price/Person:</strong> ${{ number_format((float)$event->price_per_person, 2) }}</p>
        </div>

        <div class="form-section">
            <h4>Design</h4>
            <p><strong>Theme:</strong> {{ $event->design_name }}</p>
            <p><strong>Price:</strong> ${{ number_format((float)$event->design_price, 2) }}</p>
        </div>

        <div class="form-section">
            <h4>Special Requests</h4>
            <p>{{ $event->special_requests ?: 'None' }}</p>
        </div>
    </div>

    <div class="form-section" style="background-color:#e3f2fd;">
        <h4>Total</h4>
        <p><strong>${{ number_format((float)$event->total_price, 2) }}</strong></p>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>