<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventPro - Professional Event Management</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <span>EventPro</span>
        </div>

        <nav>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            @if (function_exists('isLoggedIn') && isLoggedIn())
                @if (function_exists('isAdmin') && isAdmin())
                    <a href="{{ url('/admin') }}" class="btn btn-primary">Admin Dashboard</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">My Dashboard</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}" class="btn btn-primary">Login</a>
                <a href="{{ url('/register') }}" class="btn btn-secondary">Register</a>
            @endif
        </div>
    </div>
</header>

<section class="hero">
    <div class="container">
        <h1>Create Unforgettable Events</h1>
        <p>From intimate gatherings to grand celebrations, we handle every detail with perfection. Your dream event, our expertise.</p>

        @if (! (function_exists('isLoggedIn') && isLoggedIn()))
            <a href="{{ url('/register') }}" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                Get Started <i class="fas fa-arrow-right"></i>
            </a>
        @else
            <a href="{{ route('events.create') }}" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                Create New Event <i class="fas fa-calendar-plus"></i>
            </a>
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
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Venue Selection</h3>
                <p>Choose from our curated list of beautiful venues, each with unique charm and amenities.</p>
            </div>

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-utensils"></i></div>
                <h3>Catering Services</h3>
                <p>Delicious menus crafted by expert chefs, customizable to your taste and dietary needs.</p>
            </div>

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-palette"></i></div>
                <h3>Event Design</h3>
                <p>Transform your venue with our stunning decoration themes and design concepts.</p>
            </div>

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-users"></i></div>
                <h3>Guest Management</h3>
                <p>Comprehensive guest list management and seating arrangements for smooth events.</p>
            </div>

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                <h3>One-Stop Solution</h3>
                <p>From planning to execution, we handle every aspect of your event seamlessly.</p>
            </div>

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock assistance to ensure your event runs perfectly from start to finish.</p>
            </div>
        </div>
    </div>
</section>

<section id="about" class="services" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose EventPro?</h2>
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
                <span>EventPro</span>
            </div>
            <div>
                <p>Contact us: info@eventpro.com | +1 (555) 123-4567</p>
                <p>123 Event Street, Celebration City, EC 12345</p>
            </div>
            <div>
                <p>&copy; 2024 EventPro. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>