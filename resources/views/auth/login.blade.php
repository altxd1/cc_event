<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventPro</title>

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

                @if (function_exists('isLoggedIn') && isLoggedIn())
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="/register">Register</a></li>
                @endif
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="form-container">
        <h2 class="form-title">Login to Your Account</h2>

        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username">Username or Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    required
                    value="{{ old('username') }}"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="text-center mt-2">
                <p>Don't have an account? <a href="/register">Register here</a></p>
            </div>
        </form>
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