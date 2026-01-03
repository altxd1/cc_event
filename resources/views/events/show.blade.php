<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - BMW Events</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">BMW Events</a>
        </div>

        <nav>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Welcome, {{ session('full_name') }}</span>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

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
        <p style="font-size: 1.4rem; font-weight: bold; color: #6a11cb;">
            ${{ number_format((float)$event->total_price, 2) }}
        </p>
    </div>

    <div style="margin-top: 1rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<footer style="margin-top: 2rem;">
    <div class="container">
        <div class="footer-content">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <span>BMW Events</span>
            </div>
            <div>
                <p>&copy; 2026 BMW Events. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>