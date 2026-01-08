<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events on {{ $date->format('F j, Y') }} - BMW Events</title>
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .day-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .event-card {
            border: none;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: transform 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .event-card.approved {
            border-left: 4px solid #28a745;
        }
        
        .event-card.pending {
            border-left: 4px solid #ffc107;
        }
        
        .time-badge {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            color: #495057;
        }
        
        .venue-badge {
            background: #e7f1ff;
            color: #0d6efd;
        }
        
        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
    </style>
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
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
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

<div class="container py-4">
    <div class="day-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-2"></i> Back to Calendar
                </a>
                <h2 class="mb-1">
                    <i class="fas fa-calendar-day me-2"></i>
                    Events on {{ $date->format('l, F j, Y') }}
                </h2>
                <p class="text-muted mb-0">{{ $events->count() }} event(s) scheduled</p>
            </div>
            
            <div>
                <a href="{{ route('events.create') }}?date={{ $date->format('Y-m-d') }}" 
                   class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i> Book This Date
                </a>
            </div>
        </div>
        
        <!-- Events List -->
        @if($events->count() > 0)
            <div class="row">
                @foreach($events as $event)
                    <div class="col-md-6">
                        <div class="event-card {{ $event->status }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">
                                        {{ $event->event_name }}
                                        <span class="status-badge {{ $event->status }} badge ms-2">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </h5>
                                    <div class="time-badge">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="venue-badge badge">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $event->place->place_name ?? 'N/A' }}
                                    </span>
                                    <span class="badge bg-light text-dark ms-2">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $event->number_of_guests }} guests
                                    </span>
                                </div>
                                
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-user me-1"></i>
                                    Booked by: {{ $event->user->full_name ?? 'N/A' }}
                                </p>
                                
                                @if($event->special_requests)
                                    <div class="alert alert-light mt-2 mb-0">
                                        <small>
                                            <strong><i class="fas fa-star me-1"></i> Special Requests:</strong><br>
                                            {{ $event->special_requests }}
                                        </small>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <strong class="text-primary">
                                            {{ \App\Helpers\CurrencyHelper::format($event->total_price) }}
                                        </strong>
                                    </div>
                                    <div>
                                        <a href="{{ route('events.show', $event->event_id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                <h4>No Events Scheduled</h4>
                <p class="text-muted">There are no events booked for this date.</p>
                <a href="{{ route('events.create') }}?date={{ $date->format('Y-m-d') }}" 
                   class="btn btn-success btn-lg">
                    <i class="fas fa-plus-circle me-2"></i> Be the first to book!
                </a>
            </div>
        @endif
        
        <!-- Daily Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Total Events</h6>
                        <div class="display-6 fw-bold text-primary">{{ $events->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Total Guests</h6>
                        <div class="display-6 fw-bold text-success">
                            {{ $events->sum('number_of_guests') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <div class="display-6 fw-bold text-danger">
                            {{ \App\Helpers\CurrencyHelper::format($events->sum('total_price')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('calendar.day', $date->copy()->subDay()->format('Y-m-d')) }}" 
               class="btn btn-outline-primary">
                <i class="fas fa-chevron-left me-2"></i> Previous Day
            </a>
            
            <a href="{{ route('calendar.index', ['month' => $date->format('m'), 'year' => $date->format('Y')]) }}" 
               class="btn btn-outline-secondary">
                Back to {{ $date->format('F Y') }} Calendar
            </a>
            
            <a href="{{ route('calendar.day', $date->copy()->addDay()->format('Y-m-d')) }}" 
               class="btn btn-outline-primary">
                Next Day <i class="fas fa-chevron-right ms-2"></i>
            </a>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>