<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - EventPro</title>

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

<div class="container">
    <h2 class="section-title">Create New Event</h2>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('events.store') }}" class="form-container">
        @csrf

        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Event Information</h3>

            <div class="form-group">
                <label for="event_name">Event Name *</label>
                <input type="text" id="event_name" name="event_name" class="form-control"
                       value="{{ old('event_name') }}" required>
            </div>

            <div class="event-form-grid">
                <div class="form-group">
                    <label for="event_date">Event Date *</label>
                    <input type="date" id="event_date" name="event_date" class="form-control"
                           value="{{ old('event_date') }}"
                           required
                           min="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label for="event_time">Event Time *</label>
                    <input type="time" id="event_time" name="event_time" class="form-control"
                           value="{{ old('event_time', '18:00') }}" required>
                </div>

                <div class="form-group">
                    <label for="guests">Number of Guests *</label>
                    <input type="number" id="guests" name="guests" class="form-control"
                           value="{{ old('guests', 50) }}" min="10" max="1000" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-map-marker-alt"></i> Select Venue</h3>
            <div class="card-grid">
                @foreach ($places as $place)
                    <div class="card">
                        <div class="card-img"><i class="fas fa-building"></i></div>
                        <div class="card-content">
                            <h3 class="card-title">{{ $place->place_name }}</h3>
                            <p>{{ $place->description }}</p>
                            <p>Capacity: {{ $place->capacity }} guests</p>
                            <p class="card-price">${{ number_format((float)$place->price, 2) }}</p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="place_id"
                                       value="{{ $place->place_id }}"
                                       data-price="{{ (float)$place->price }}"
                                       required
                                       @checked(old('place_id') == $place->place_id)>
                                Select This Venue
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-utensils"></i> Select Menu</h3>
            <div class="card-grid">
                @foreach ($foodItems as $food)
                    <div class="card">
                        <div class="card-img"><i class="fas fa-hamburger"></i></div>
                        <div class="card-content">
                            <h3 class="card-title">{{ $food->food_name }}</h3>
                            <p>{{ $food->description }}</p>
                            <p class="card-price">${{ number_format((float)$food->price_per_person, 2) }} per person</p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="food_id"
                                       value="{{ $food->food_id }}"
                                       data-price-per-person="{{ (float)$food->price_per_person }}"
                                       required
                                       @checked(old('food_id') == $food->food_id)>
                                Select This Menu
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-palette"></i> Select Design Theme</h3>
            <div class="card-grid">
                @foreach ($designs as $design)
                    <div class="card">
                        <div class="card-img"><i class="fas fa-paint-brush"></i></div>
                        <div class="card-content">
                            <h3 class="card-title">{{ $design->design_name }}</h3>
                            <p>{{ $design->description }}</p>
                            <p class="card-price">${{ number_format((float)$design->price, 2) }}</p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="design_id"
                                       value="{{ $design->design_id }}"
                                       data-price="{{ (float)$design->price }}"
                                       required
                                       @checked(old('design_id') == $design->design_id)>
                                Select This Design
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-star"></i> Additional Information</h3>
            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" class="form-control" rows="4">{{ old('special_requests') }}</textarea>
                <small>Any special requirements or notes for your event</small>
            </div>
        </div>

        <div class="form-section" style="background-color:#e3f2fd;">
            <h3><i class="fas fa-calculator"></i> Price Summary</h3>
            <div id="price-summary">
                <p>Select options above to see pricing details</p>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-success" style="padding: 1rem 3rem; font-size: 1.2rem;">
                <i class="fas fa-check-circle"></i> Submit Event Request
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 1rem 3rem; font-size: 1.2rem;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculatePrice() {
        const guests = parseInt(document.getElementById('guests').value) || 50;

        const selectedPlace = document.querySelector('input[name="place_id"]:checked');
        const selectedFood = document.querySelector('input[name="food_id"]:checked');
        const selectedDesign = document.querySelector('input[name="design_id"]:checked');

        if (selectedPlace && selectedFood && selectedDesign) {
            const placePrice = parseFloat(selectedPlace.dataset.price || '0');
            const foodPrice = parseFloat(selectedFood.dataset.pricePerPerson || '0');
            const designPrice = parseFloat(selectedDesign.dataset.price || '0');

            const total = placePrice + (foodPrice * guests) + designPrice;

            document.getElementById('price-summary').innerHTML = `
                <div style="font-size: 1.2rem;">
                    <p>Venue: $${placePrice.toFixed(2)}</p>
                    <p>Food (${guests} guests × $${foodPrice.toFixed(2)}): $${(foodPrice * guests).toFixed(2)}</p>
                    <p>Design: $${designPrice.toFixed(2)}</p>
                    <hr>
                    <p style="font-weight: bold; font-size: 1.5rem; color: #6a11cb;">
                        Total: $${total.toFixed(2)}
                    </p>
                </div>
            `;
        }
    }

    document.getElementById('guests').addEventListener('input', calculatePrice);
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    calculatePrice();
});
</script>
</body>
</html>