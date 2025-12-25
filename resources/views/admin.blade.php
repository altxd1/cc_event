<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventPro</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">EventPro</a>
        </div>

        <nav>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                <li><a href="{{ route('admin.items', ['type' => 'food']) }}">Manage Items</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Admin: {{ session('full_name') }}</span>
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
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="active">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.events.index') }}">
                            <i class="fas fa-calendar-alt"></i> Manage Events
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'food']) }}">
                            <i class="fas fa-utensils"></i> Food Items
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'places']) }}">
                            <i class="fas fa-map-marker-alt"></i> Event Places
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'designs']) }}">
                            <i class="fas fa-palette"></i> Event Designs
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Admin Dashboard</h2>
            <p>Welcome to the EventPro administration panel</p>

            <div class="card-grid mt-3">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Events</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #6a11cb;">
                            {{ $totalEvents }}
                        </p>
                        <p>All events created</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Pending Events</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #ffc107;">
                            {{ $pendingEvents }}
                        </p>
                        <p>Awaiting approval</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Users</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #28a745;">
                            {{ $totalUsers }}
                        </p>
                        <p>Registered users</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Revenue</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #dc3545;">
                            ${{ number_format((float)$revenue, 2) }}
                        </p>
                        <p>Total from approved events</p>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <h3>Recent Events</h3>

                @if ($recentEvents->isEmpty())
                    <div class="alert alert-info">No events found.</div>
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
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($recentEvents as $event)
                                <tr>
                                    <td>{{ $event->event_name }}</td>
                                    <td>
                                        {{ $event->full_name }}<br>
                                        <small>{{ $event->email }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
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
                                            {{ ucfirst($event->status ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.events.show', $event->event_id) }}"
                                           class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-3">
                <h3>Quick Actions</h3>

                <div class="card-grid">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Approve Events</h3>
                            <p>Review and approve pending event requests</p>
                            <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Go to Pending Events
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Add New Food Item</h3>
                            <p>Add new menu options for events</p>
                        <a href="{{ route('admin.items.create', ['type' => 'food']) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Food Item
                        </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Manage Venues</h3>
                            <p>Add or update event venues</p>
                            <a href="{{ route('admin.items', ['type' => 'places']) }}" class="btn btn-primary">
                                <i class="fas fa-building"></i> Manage Venues
                            </a>
                        </div>
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