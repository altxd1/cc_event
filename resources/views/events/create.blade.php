<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - BMW Events</title>

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

<div class="container ce-wrap">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
        <h2 class="section-title" style="margin:0;">Create New Event</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error" style="margin-top: 1rem;">{{ $errors->first() }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" style="margin-top: 1rem;">{{ session('success') }}</div>
    @endif

    <div class="ce-layout" style="margin-top: 1rem;">
        {{-- MAIN FORM --}}
        <div class="ce-panel">
            <form method="POST" action="{{ route('events.store') }}">
                @csrf

                <div class="form-section" style="padding:0; border:none;">
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
                            <label for="guests">Guests *</label>
                            <input type="number" id="guests" name="guests" class="form-control"
                                   value="{{ old('guests', 50) }}" min="10" max="1000" required>
                        </div>
                    </div>
                </div>

                {{-- VENUE --}}
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Choose a Venue</h3>

                    <div class="ce-grid">
                        @foreach ($places as $place)
                            @php
                                $img = !empty($place->image_path) ? asset('storage/'.$place->image_path) : null;
                                $price = (float)$place->price;
                                $cap = (int)($place->capacity ?? 0);
                            @endphp

                            <div class="ce-card select-card"
                                 data-group="place"
                                 data-name="{{ $place->place_name }}"
                                 data-price="{{ $price }}"
                                 data-capacity="{{ $cap }}">
                                <div class="ce-badge">${{ number_format($price, 2) }}</div>
                                <div class="ce-selected">Selected</div>

                                <div class="ce-media">
                                    <img
                                       src="{{ asset('images/venues/venue-'.$place->place_id.'.jpeg') }}"
                                       alt="{{ $place->place_name }}"
                                        onerror="this.onerror=null; this.src='{{ asset('images/venues/default.jpeg') }}';"
                                      >
                                        </div>

                                <div class="ce-body">
                                    <h4 class="ce-title">{{ $place->place_name }}</h4>
                                    <p class="ce-desc">{{ \Illuminate\Support\Str::limit($place->description ?? '', 80) }}</p>
                                    <p class="ce-desc" style="margin-top:.5rem;"><strong>Capacity:</strong> {{ $cap }} guests</p>

                                    <input class="ce-radio"
                                           type="radio"
                                           name="place_id"
                                           value="{{ $place->place_id }}"
                                           required
                                           @checked(old('place_id') == $place->place_id)>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FOOD --}}
                <div class="form-section">
                    <h3><i class="fas fa-utensils"></i> Choose a Menu</h3>

                    <div class="ce-grid">
                        @foreach ($foodItems as $food)
                            @php
                                $img = !empty($food->image_path) ? asset('storage/'.$food->image_path) : null;
                                $pp = (float)$food->price_per_person;
                            @endphp

                            <div class="ce-card select-card"
                                 data-group="food"
                                 data-name="{{ $food->food_name }}"
                                 data-price-per-person="{{ $pp }}">
                                <div class="ce-badge">${{ number_format($pp, 2) }} / person</div>
                                <div class="ce-selected">Selected</div>

                                <div class="ce-media">
                                 <img
                                    src="{{ asset('images/foods/food-'.$food->food_id.'.jpeg') }}"
                                    alt="{{ $food->food_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('images/foods/default.jpeg') }}';"
                                        >
                                        </div>

                                <div class="ce-body">
                                    <h4 class="ce-title">{{ $food->food_name }}</h4>
                                    <p class="ce-desc">{{ \Illuminate\Support\Str::limit($food->description ?? '', 80) }}</p>

                                    <input class="ce-radio"
                                           type="radio"
                                           name="food_id"
                                           value="{{ $food->food_id }}"
                                           required
                                           @checked(old('food_id') == $food->food_id)>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- DESIGN --}}
                <div class="form-section">
                    <h3><i class="fas fa-palette"></i> Choose a Design Theme</h3>

                    <div class="ce-grid">
                        @foreach ($designs as $design)
                            @php
                                $img = !empty($design->image_path) ? asset('storage/'.$design->image_path) : null;
                                $price = (float)$design->price;
                            @endphp

                            <div class="ce-card select-card"
                                 data-group="design"
                                 data-name="{{ $design->design_name }}"
                                 data-price="{{ $price }}">
                                <div class="ce-badge">${{ number_format($price, 2) }}</div>
                                <div class="ce-selected">Selected</div>

                                <div class="ce-media">
                                <img
                                    src="{{ asset('images/designs/design-'.$design->design_id.'.jpeg') }}"
                                    alt="{{ $design->design_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('images/designs/default.jpeg') }}';"
                                >
                            </div>

                                <div class="ce-body">
                                    <h4 class="ce-title">{{ $design->design_name }}</h4>
                                    <p class="ce-desc">{{ \Illuminate\Support\Str::limit($design->description ?? '', 80) }}</p>

                                    <input class="ce-radio"
                                           type="radio"
                                           name="design_id"
                                           value="{{ $design->design_id }}"
                                           required
                                           @checked(old('design_id') == $design->design_id)>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                <div class="form-section">
                    <h3><i class="fas fa-star"></i> Additional Information</h3>

                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea id="special_requests" name="special_requests" class="form-control" rows="4">{{ old('special_requests') }}</textarea>
                        <small>Any special requirements or notes for your event</small>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success" style="padding: 1rem 3rem; font-size: 1.1rem;">
                        <i class="fas fa-check-circle"></i> Submit Event Request
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- SUMMARY --}}
        <aside class="ce-aside">
            <div class="ce-panel">
                <h3 class="sum-title"><i class="fas fa-calculator"></i> Summary</h3>

                <div class="sum-line">
                    <div><strong>Venue</strong><small id="sum-place-name">Not selected</small></div>
                    <div id="sum-place-price">$0.00</div>
                </div>

                <div class="sum-line">
                    <div><strong>Menu</strong><small id="sum-food-name">Not selected</small></div>
                    <div id="sum-food-price">$0.00</div>
                </div>

                <div class="sum-line">
                    <div><strong>Design</strong><small id="sum-design-name">Not selected</small></div>
                    <div id="sum-design-price">$0.00</div>
                </div>

                <div class="sum-total">
                    <div><strong>Total</strong></div>
                    <div class="value" id="sum-total">$0.00</div>
                </div>

                <div class="sum-warning" id="cap-warning"></div>

                <p class="sum-hint">Click any card to select it. Total updates automatically.</p>
            </div>
        </aside>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.select-card');
    const guestsEl = document.getElementById('guests');

    const sumPlaceName = document.getElementById('sum-place-name');
    const sumPlacePrice = document.getElementById('sum-place-price');
    const sumFoodName = document.getElementById('sum-food-name');
    const sumFoodPrice = document.getElementById('sum-food-price');
    const sumDesignName = document.getElementById('sum-design-name');
    const sumDesignPrice = document.getElementById('sum-design-price');
    const sumTotal = document.getElementById('sum-total');
    const capWarn = document.getElementById('cap-warning');

    function money(x){
        const n = Number(x || 0);
        return '$' + n.toFixed(2);
    }

    function getSelected(group) {
        return document.querySelector(`.select-card.is-selected[data-group="${group}"]`);
    }

    function syncSelectedFromRadios() {
        document.querySelectorAll('.select-card').forEach(c => c.classList.remove('is-selected'));

        ['place','food','design'].forEach(group => {
            const checked = document.querySelector(`input[name="${group}_id"]:checked`);
            if (!checked) return;
            const card = checked.closest('.select-card');
            if (card) card.classList.add('is-selected');
        });
    }

    function renderSummary() {
        const guests = parseInt(guestsEl?.value || '0', 10) || 0;

        const place = getSelected('place');
        const food = getSelected('food');
        const design = getSelected('design');

        let placePrice = 0, foodPP = 0, designPrice = 0;

        if (place) {
            sumPlaceName.textContent = place.dataset.name || 'Selected';
            placePrice = parseFloat(place.dataset.price || '0');
            sumPlacePrice.textContent = money(placePrice);
        } else {
            sumPlaceName.textContent = 'Not selected';
            sumPlacePrice.textContent = money(0);
        }

        if (food) {
            sumFoodName.textContent = food.dataset.name || 'Selected';
            foodPP = parseFloat(food.dataset.pricePerPerson || '0');
            sumFoodPrice.textContent = guests ? money(foodPP * guests) : money(0);
        } else {
            sumFoodName.textContent = 'Not selected';
            sumFoodPrice.textContent = money(0);
        }

        if (design) {
            sumDesignName.textContent = design.dataset.name || 'Selected';
            designPrice = parseFloat(design.dataset.price || '0');
            sumDesignPrice.textContent = money(designPrice);
        } else {
            sumDesignName.textContent = 'Not selected';
            sumDesignPrice.textContent = money(0);
        }

        const total = placePrice + (foodPP * guests) + designPrice;
        sumTotal.textContent = money(total);

        // capacity warning
        if (place) {
            const cap = parseInt(place.dataset.capacity || '0', 10) || 0;
            if (cap > 0 && guests > cap) {
                capWarn.style.display = 'block';
                capWarn.textContent = `Warning: guests (${guests}) exceed venue capacity (${cap}).`;
            } else {
                capWarn.style.display = 'none';
                capWarn.textContent = '';
            }
        } else {
            capWarn.style.display = 'none';
            capWarn.textContent = '';
        }
    }

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const group = card.dataset.group;
            const radio = card.querySelector('input.ce-radio');
            if (!radio) return;

            document.querySelectorAll(`.select-card[data-group="${group}"]`)
                .forEach(c => c.classList.remove('is-selected'));

            radio.checked = true;
            card.classList.add('is-selected');

            renderSummary();
        });
    });

    if (guestsEl) {
        guestsEl.addEventListener('input', renderSummary);
        guestsEl.addEventListener('change', renderSummary);
    }

    syncSelectedFromRadios();
    renderSummary();
});
</script>
</body>
</html>