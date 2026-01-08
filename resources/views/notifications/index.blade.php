<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - BMW Events</title>
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
                <li><a href="/">Home</a></li>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('events.create') }}">Create Event</a></li>
                <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
                <li><a href="{{ route('notifications.index') }}" class="active">Notifications</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">{{ __('messages.welcome', ['name' => session('full_name') ?? '']) }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">{{ __('messages.logout') }}</button>
            </form>
        </div>
    </div>
</header>

<div class="container" style="margin-top: 2rem;">
    <h2>Notifications</h2>
    @if ($notifications->isEmpty())
        <div class="alert alert-info">You have no notifications.</div>
    @else
        <ul class="list-group">
            @foreach ($notifications as $notification)
                <li class="list-group-item" style="margin-bottom: 0.5rem; {{ $notification->read_at ? 'opacity:0.6;' : '' }}">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span>
                            {{ $notification->data['message'] ?? 'Notification' }}
                            <br>
                            <small>{{ $notification->created_at->format('Y-m-d H:i') }}</small>
                        </span>
                        @if (!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Mark as read</button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
</body>
</html>