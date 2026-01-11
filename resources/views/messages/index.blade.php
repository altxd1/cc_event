<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - BMW Events</title>
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
                @if(!empty($isAdmin) && $isAdmin)
                    <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    {{-- <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                    <li><a href="{{ route('admin.items') }}">Manage Items</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}">Calendar</a></li> --}}
                    <li><a href="{{ route('admin.messages.create') }}">Compose</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    {{-- <li><a href="{{ route('events.create') }}">Create Event</a></li>
                    <li><a href="{{ route('calendar.index') }}">Calendar</a></li> --}}
                    <li><a href="{{ route('messages.create') }}">Compose</a></li>
                @endif
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
    <h2>Inbox</h2>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($messages->isEmpty())
        <div class="alert alert-info">You have no messages.</div>
    @else
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($messages as $message)
                    <tr>
                        <td>{{ $message->sender->full_name }}</td>
                        <td>{{ $message->subject ?: '(No subject)' }}</td>
                        <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</body>
</html>