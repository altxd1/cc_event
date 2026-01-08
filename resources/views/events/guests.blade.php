<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guests - {{ $event->event_name }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="/" style="color: white; text-decoration: none;">BMW Events</a>
        </div>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                @if (function_exists('isAdmin') && isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @endif
            </ul>
        </nav>
        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">
                {{ function_exists('isAdmin') && isAdmin() ? 'Admin Panel' : 'Welcome, '.(session('full_name') ?? 'User') }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container" style="margin-top: 1rem;">
    <h2>Guest List for "{{ $event->event_name }}"</h2>
    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="mb-3">
        <a href="{{ route('events.show', ['id' => $event->event_id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Event
        </a>
        <a href="{{ route('events.guests.export', ['eventId' => $event->event_id]) }}" class="btn btn-primary">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>

    <!-- Import CSV form -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-file-import"></i> Import Guests (CSV)</div>
        <div class="card-body">
            <form method="POST" action="{{ route('events.guests.import', ['eventId' => $event->event_id]) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="file" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-success mt-2"><i class="fas fa-upload"></i> Upload</button>
            </form>
            <small class="text-muted">CSV should have columns: Name, Email</small>
        </div>
    </div>

    <!-- Guest list table -->
    @if ($guests->isEmpty())
        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No guests added yet.</div>
    @else
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($guests as $guest)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $guest->name }}</td>
                        <td>{{ $guest->email }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</body>
</html>