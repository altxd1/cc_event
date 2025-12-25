<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - EventPro</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="/" style="color: white; text-decoration: none;">EventPro</a>
        </div>

        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/events/create">Create Event</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">
                Welcome, {{ session('full_name') }}
            </span>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="/events/create"><i class="fas fa-calendar-plus"></i> Create New Event</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Events</h2>
            <p>Here you can view and manage all your event bookings.</p>

            <div class="text-right mb-2">
                <a href="/events/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>

            @if ($events->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You haven't created any events yet.
                    <a href="/events/create">Create your first event!</a>
                </div>
            @else
                @php
                    $status_colors = [
                        'pending' => '#ffc107',
                        'approved' => '#28a745',
                        'rejected' => '#dc3545',
                        'completed' => '#007bff',
                    ];
                @endphp

                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Guests</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $event->event_name }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}<br>
                                    {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                                </td>
                                <td>{{ $event->place_name }}</td>
                                <td>{{ $event->number_of_guests }}</td>
                                <td>${{ number_format((float)$event->total_price, 2) }}</td>
                                <td>
                                    @php $color = $status_colors[$event->status] ?? '#6c757d'; @endphp
                                    <span style="
                                        background-color: {{ $color }};
                                        color: white;
                                        padding: 0.25rem 0.5rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: bold;
                                    ">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-primary" style="padding: 0.25rem 0.5rem;"
                                    href="{{ route('events.show', $event->event_id) }}">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="card-grid mt-3">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Events</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #6a11cb;">
                            {{ $totalEvents }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Upcoming Events</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #28a745;">
                            {{ $upcomingEvents }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Pending Approval</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #ffc107;">
                            {{ $pendingEvents }}
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <span>EventPro</span>
            </div>
            <div>
                <p>&copy; 2024 EventPro. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>