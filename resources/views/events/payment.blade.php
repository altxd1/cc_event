<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.pay_for_event') }} - BMW Events</title>

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
                <li><a href="/">{{ __('messages.home') }}</a></li>
                @if (function_exists('isAdmin') && isAdmin())
                    <!-- Admin navigation -->
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li><a href="{{ route('admin.events.index') }}">{{ __('messages.manage_events') }}</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}">{{ __('messages.calendar') }}</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'food']) }}">{{ __('messages.manage_items') }}</a></li>
                @elseif (function_exists('isLoggedIn') && isLoggedIn())
                    <!-- Logged in user navigation -->
                    <li><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li><a href="{{ route('events.create') }}">{{ __('messages.create_event') }}</a></li>
                    <li><a href="{{ route('calendar.index') }}">{{ __('messages.calendar') }}</a></li>
                @endif
            </ul>
        </nav>

        <div class="auth-buttons">
            @if (function_exists('isLoggedIn') && isLoggedIn())
                <span style="color: white; margin-right: 1rem;">
                    {{ function_exists('isAdmin') && isAdmin() ? __('messages.admin_panel') : __('messages.welcome', ['name' => session('full_name') ?? '']) }}
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">{{ __('messages.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login.form') }}" class="btn btn-primary">{{ __('messages.login') }}</a>
                <a href="{{ route('register.form') }}" class="btn btn-secondary">{{ __('messages.register') }}</a>
            @endif
        </div>
    </div>
</header>

<div class="container" style="margin-top: 2rem;">
    <h2>{{ __('messages.pay_for_event') }}</h2>
    <div class="card">
        <div class="card-content">
            <p><strong>{{ __('messages.event_name') }}:</strong> {{ $event->event_name }}</p>
            <p><strong>{{ __('messages.event_date') }}:</strong> {{ $event->event_date->format('Y-m-d') }} {{ $event->event_time }}</p>
            <p><strong>{{ __('messages.total_amount') }}:</strong> {{ \App\Helpers\CurrencyHelper::format($event->total_price) }}</p>

            <form method="POST" action="{{ route('events.payment.process', ['eventId' => $event->event_id]) }}">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('messages.pay_now') }}</button>
            </form>
        </div>
    </div>
</div>

<footer style="margin-top: 4rem;">
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