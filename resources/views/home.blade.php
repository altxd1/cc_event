<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMW Events - Professional Event Management</title>

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
        <!-- Default navigation links accessible to all visitors -->
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/') }}#services">Services</a></li>
        <li><a href="{{ url('/') }}#about">About</a></li>
        <li><a href="{{ url('/') }}#contact">Contact</a></li>

        @if (function_exists('isAdmin') && isAdmin())
            <!-- Admin navigation: provide a single link to the admin dashboard -->
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <!-- Admin-specific management links are available in the admin panel sidebar -->
        @elseif (function_exists('isLoggedIn') && isLoggedIn())
            <!-- Logged in user navigation -->
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            {{-- <li><a href="{{ route('events.create') }}">Create Event</a></li> --}}
            <!-- Calendar, Inbox, Notifications are accessible via the dashboard sidebar -->
        @endif
    </ul>
    </nav>

        <div class="auth-buttons">
            @if (function_exists('isLoggedIn') && isLoggedIn())
                <span style="color: white; margin-right: 1rem;">
                    {{ function_exists('isAdmin') && isAdmin() ? 'Admin Panel' : 'Welcome, '.(session('full_name') ?? 'User') }}
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register.form') }}" class="btn btn-secondary">Register</a>
            @endif
        </div>
    </div>
</header>
<section class="hero hero-bg"
    style="background-image: url('{{ asset('images/home.jpeg') }}');">
    <div class="container hero-content">
        <h1>Create Unforgettable Events</h1>
        <p>From intimate gatherings to grand celebrations, we handle every detail with perfection.</p>
        @if (! (function_exists('isLoggedIn') && isLoggedIn()))
    <a href="{{ url('/register') }}" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
        Get Started <i class="fas fa-arrow-right"></i>
    </a>
            @else
                @if (function_exists('isAdmin') && isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                        Go to Admin Dashboard <i class="fas fa-gauge-high"></i>
                    </a>
                @else
                    <a href="{{ route('events.create') }}" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                        Create New Event <i class="fas fa-calendar-plus"></i>
                    </a>
                @endif
            @endif
    </div>
</section>

<section id="services" class="services">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
            <p>Comprehensive event management solutions tailored to your needs</p>
        </div>

        <div class="services-grid">
            {{-- Venue --}}
            <div class="service-card service-card--slideshow"
                 data-images="{{ asset('images/services/venue-1.jpeg') }},{{ asset('images/services/venue-2.jpeg') }},{{ asset('images/services/venue-3.jpeg') }}">
                <div class="service-media">
                    <img src="{{ asset('images/services/venue-1.jpeg') }}" alt="Venue Selection">
                    <div class="service-icon"><i class="fas fa-map-marker-alt"></i></div>
                </div>
                <h3>Venue Selection</h3>
                <p>Choose from our curated list of beautiful venues, each with unique charm and amenities.</p>
            </div>

            {{-- Catering --}}
            <div class="service-card service-card--slideshow"
                 data-images="{{ asset('images/services/catering-1.jpeg') }},{{ asset('images/services/catering-2.jpeg') }},{{ asset('images/services/catering-3.jpeg') }}">
                <div class="service-media">
                    <img src="{{ asset('images/services/catering-1.jpeg') }}" alt="Catering Services">
                    <div class="service-icon"><i class="fas fa-utensils"></i></div>
                </div>
                <h3>Catering Services</h3>
                <p>Delicious menus crafted by expert chefs, customizable to your taste and dietary needs.</p>
            </div>

            {{-- Design --}}
            <div class="service-card service-card--slideshow"
                 data-images="{{ asset('images/services/desing-1.jpeg') }},{{ asset('images/services/desing-2.jpeg') }},{{ asset('images/services/desing-3.jpeg') }}">
                <div class="service-media">
                    <img src="{{ asset('images/services/desing-1.jpeg') }}" alt="Event Design">
                    <div class="service-icon"><i class="fas fa-palette"></i></div>
                </div>
                <h3>Event Design</h3>
                <p>Transform your venue with our stunning decoration themes and design concepts.</p>
            </div>

            {{-- Guests --}}
            <div class="service-card service-card--slideshow"
                 data-images="{{ asset('images/services/guest-1.jpeg') }},{{ asset('images/services/guest-2.jpeg') }},{{ asset('images/services/guest-3.jpeg') }}">
                <div class="service-media">
                    <img src="{{ asset('images/services/guest-1.jpeg') }}" alt="Guest Management">
                    <div class="service-icon"><i class="fas fa-users"></i></div>
                </div>
                <h3>Guest Management</h3>
                <p>Comprehensive guest list management and seating arrangements for smooth events.</p>
            </div>
        </div>
    </div>
</section>

<section id="about" class="services" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose BMW Events?</h2>
            <p>Experience the difference with our professional event management</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-img"><i class="fas fa-bolt"></i></div>
                <div class="card-content">
                    <h3 class="card-title">Quick & Easy Booking</h3>
                    <p>Book your entire event online in minutes with our streamlined process.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-img"><i class="fas fa-shield-alt"></i></div>
                <div class="card-content">
                    <h3 class="card-title">Trusted Partners</h3>
                    <p>We work only with verified venues and vendors for quality assurance.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-img"><i class="fas fa-dollar-sign"></i></div>
                <div class="card-content">
                    <h3 class="card-title">Transparent Pricing</h3>
                    <p>No hidden costs. See exactly what you're paying for with itemized quotes.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-img"><i class="fas fa-calendar-check"></i></div>
                <div class="card-content">
                    <h3 class="card-title">Real-time Tracking</h3>
                    <p>Monitor your event planning progress through our dashboard anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer id="contact">
    <div class="container">
        <div class="footer-content">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <span>BMW Events </span>
            </div>
            <div>
                <p>Contact us: wissalevent@gmail.com| +212 708090405</p>
                <p>Hay rabat , Tangier City, Rue N 7 </p>
            </div>
            <div>
                <p>&copy; 2026 BMW Events. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log("services slideshow script loaded");

    const cards = document.querySelectorAll('.service-card--slideshow');
    console.log("cards found:", cards.length);

    cards.forEach((card, idx) => {
        const img = card.querySelector('.service-media img');
        const list = (card.dataset.images || '')
            .split(',')
            .map(s => s.trim())
            .filter(Boolean);

        console.log("card", idx, "images:", list);

        if (!img || list.length < 2) return;

        list.forEach(src => { const i = new Image(); i.src = src; });

        let index = 0;
        let timer = null;

        const start = () => {
            console.log("mouseenter on card", idx);
            if (timer) return;
            timer = setInterval(() => {
                index = (index + 1) % list.length;
                img.src = list[index];
                console.log("changed to:", img.src);
            }, 3000);
        };

        const stop = () => {
            console.log("mouseleave on card", idx);
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
            index = 0;
            img.src = list[0];
        };

        card.addEventListener('mouseenter', start);
        card.addEventListener('mouseleave', stop);
    });
});
</script>
</body>
</html>