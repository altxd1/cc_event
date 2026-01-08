<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Admin</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
<div class="container" style="margin-top: 2rem;">
    <h2>Event Details - {{ $event->event_name }}</h2>

    <div class="event-form-grid">
        <div class="form-section">
            <h4>Customer Information</h4>
            <p><strong>Name:</strong> {{ $event->full_name }}</p>
            <p><strong>Email:</strong> {{ $event->email }}</p>
            @if (!empty($event->phone))
                <p><strong>Phone:</strong> {{ $event->phone }}</p>
            @endif
        </div>

        <div class="form-section">
            <h4>Event Details</h4>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</p>
            <p><strong>Guests:</strong> {{ $event->number_of_guests }}</p>
            <p><strong>Status:</strong> {{ ucfirst($event->status) }}</p>
        </div>

        <div class="form-section">
            <h4>Venue</h4>
            <p><strong>Place:</strong> {{ $event->place_name }}</p>
            <p><strong>Capacity:</strong> {{ $event->place_capacity }} guests</p>
            <p><strong>Price:</strong> {{ \App\Helpers\CurrencyHelper::format($event->place_price) }}</p>
        </div>

        <div class="form-section">
            <h4>Food & Design</h4>
            <p><strong>Menu:</strong> {{ $event->food_name }}</p>
            <p><strong>Food Price/Person:</strong> {{ \App\Helpers\CurrencyHelper::format($event->food_price_per_person) }}</p>
            <p><strong>Design:</strong> {{ $event->design_name }}</p>
           <p><strong>Design Price:</strong> {{ \App\Helpers\CurrencyHelper::format($event->design_price) }}</p>
            <p><strong>Special Requests:</strong> {{ $event->special_requests ?: 'None' }}</p>
        </div>
    </div>

   <div class="form-section" style="background-color:#e3f2fd;">
    <h4>Pricing Breakdown</h4>
    <p>Venue: {{ \App\Helpers\CurrencyHelper::format($event->place_price) }}</p>
    <p>
        Food ({{ $event->number_of_guests }} × {{ \App\Helpers\CurrencyHelper::format($event->food_price_per_person) }}):
        {{ \App\Helpers\CurrencyHelper::format($event->food_price_per_person * (int)$event->number_of_guests) }}
    </p>
    <p>Design: {{ \App\Helpers\CurrencyHelper::format($event->design_price) }}</p>
    <hr>
    <p><strong>Total: {{ \App\Helpers\CurrencyHelper::format($event->total_price) }}</strong></p>
</div>

    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Back</a>
</div>
</body>
</html>