<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - BMW Events Admin</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .btn {
            background-color: #e9ecef !important;
            color: #495057 !important;
            border: 2px solid #adb5bd !important;
            margin: 5px !important;
            padding: 10px 20px !important;
            display: inline-block !important;
            cursor: pointer;
        }
        .btn-primary { background-color:#6a11cb !important; color:white !important; border-color:#6a11cb !important; }
        .btn-success { background-color:#28a745 !important; color:white !important; border-color:#28a745 !important; }
        .btn-danger  { background-color:#dc3545 !important; color:white !important; border-color:#dc3545 !important; }
    </style>
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">BMW Events</a>
        </div>
        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Admin Panel</span>
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
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') && !request()->routeIs('admin.calendar.*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}" class="{{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}"><i class="fas fa-calendar"></i> Calendar</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'food']) }}" class="{{ request()->fullUrlIs('*type=food*') ? 'active' : '' }}"><i class="fas fa-utensils"></i> Food Items</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'places']) }}" class="{{ request()->fullUrlIs('*type=places*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'designs']) }}" class="{{ request()->fullUrlIs('*type=designs*') ? 'active' : '' }}"><i class="fas fa-palette"></i> Event Designs</a></li>
                    <li><a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}"><i class="fas fa-gift"></i> Packages</a></li>
                </ul>
            </nav>
        </aside>
        <main class="dashboard-content">
            <h2>Manage Packages</h2>
            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="text-right mb-3">
                <a href="{{ route('admin.packages.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Package
                </a>
            </div>

            @if ($packages->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No packages found. <a href="{{ route('admin.packages.create') }}">Add your first package!</a>
                </div>
            @else
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price (MAD)</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($packages as $pkg)
                            <tr>
                                <td>#{{ $pkg->package_id }}</td>
                                <td>{{ $pkg->package_name }}</td>
                                <td>{{ \App\Helpers\CurrencyHelper::format($pkg->price) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($pkg->description, 50) }}</td>
                                <td>
                                    <div style="display:flex; gap:0.25rem;">
                                        <a href="{{ route('admin.packages.edit', ['id' => $pkg->package_id]) }}" class="btn btn-primary" style="padding:0.25rem 0.5rem;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.packages.delete', ['id' => $pkg->package_id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" style="padding:0.25rem 0.5rem;" onclick="return confirm('Delete this package?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
</div>
</body>
</html>