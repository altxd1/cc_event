<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Message - BMW Events</title>
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
                    <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                    <li><a href="{{ route('admin.items') }}">Manage Items</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}">Calendar</a></li>
                    <li><a href="{{ route('admin.messages.index') }}">Inbox</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('events.create') }}">Create Event</a></li>
                    <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
                    <li><a href="{{ route('messages.index') }}">Inbox</a></li>
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
    <h2>Compose Message</h2>
    <form method="POST" action="{{ (!empty($isAdmin) && $isAdmin) ? route('admin.messages.store') : route('messages.store') }}">
        @csrf
        <div class="mb-3">
            <label for="receiver_id" class="form-label">Recipient</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="" disabled selected>Select recipient</option>
                @foreach ($recipients as $recipient)
                    <option value="{{ $recipient->user_id }}">{{ $recipient->full_name }}</option>
                @endforeach
            </select>
            @error('receiver_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}">
            @error('subject')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="body" class="form-label">Message</label>
            <textarea name="body" id="body" class="form-control" rows="5" required>{{ old('body') }}</textarea>
            @error('body')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
</body>
</html>