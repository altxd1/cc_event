<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events for {{ $selectedDate->format('F j, Y') }} - Admin</title>
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">BMW Events Admin</a>
        </div>
        
        <nav>
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                <li><a href="{{ route('admin.calendar.index') }}">Calendar</a></li>
            </ul>
        </nav>
        
        <div class="auth-buttons">
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
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.events.index') }}"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}" class="active"><i class="fas fa-calendar"></i> Calendar</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="dashboard-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>
                        Events for {{ $selectedDate->format('F j, Y') }}
                    </h2>
                    <p class="text-muted mb-0">{{ $events->count() }} total events</p>
                </div>
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Calendar
                </a>
            </div>
            
            @if($events->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No events scheduled for {{ $selectedDate->format('F j, Y') }}
                </div>
            @else
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'approved' ? 'success' : 'danger') }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $event->event_name }}</h5>
                                    <span class="badge bg-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Time</small>
                                            <div><strong>{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</strong></div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Guests</small>
                                            <div><strong>{{ $event->number_of_guests }}</strong></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Venue</small>
                                        <div><strong>{{ $event->place->place_name ?? 'N/A' }}</strong></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Customer</small>
                                        <div>
                                            <strong>{{ $event->user->full_name ?? 'N/A' }}</strong><br>
                                            <small>{{ $event->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Total</small>
                                        <div><strong>${{ number_format($event->total_price, 2) }}</strong></div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.events.show', $event->event_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                        
                                        @if($event->status === 'pending')
                                            <div class="btn-group">
                                                <form method="POST" action="{{ route('admin.events.approve', $event->event_id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this event?')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.events.reject', $event->event_id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this event?')">
                                                        <i class="fas fa-times me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Calendar
                </a>
            </div>
        </main>
    </div>
</div>
</body>
</html>