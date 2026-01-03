<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage {{ $cfg['title'] }} - BMW Events Admin</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .btn {
            background-color: #e9ecef !important;
            color: #495057 !important;
            border: 2px solid #adb5bd !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin: 5px !important;
            padding: 10px 20px !important;
            display: inline-block !important;
        }
        .btn-primary { background-color:#6a11cb !important; color:white !important; border-color:#6a11cb !important; }
        .btn-success { background-color:#28a745 !important; color:white !important; border-color:#28a745 !important; }
    </style>
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
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                <li><a href="{{ route('admin.items', ['type' => $type]) }}" class="active">Manage Items</a></li>
            </ul>
        </nav>
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
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.events.index') }}"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>

                    <li>
                        <a href="{{ route('admin.items', ['type' => 'food']) }}" class="{{ $type === 'food' ? 'active' : '' }}">
                            <i class="fas fa-utensils"></i> Food Items
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'places']) }}" class="{{ $type === 'places' ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i> Event Places
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'designs']) }}" class="{{ $type === 'designs' ? 'active' : '' }}">
                            <i class="fas fa-palette"></i> Event Designs
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Manage {{ $cfg['title'] }}</h2>

            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="tab-container">
                <a href="{{ route('admin.items', ['type' => 'food']) }}" class="tab-button {{ $type === 'food' ? 'tab-active' : 'tab-inactive' }}">
                    <i class="fas fa-utensils"></i> Food Items
                </a>

                <a href="{{ route('admin.items', ['type' => 'places']) }}" class="tab-button {{ $type === 'places' ? 'tab-active' : 'tab-inactive' }}">
                    <i class="fas fa-map-marker-alt"></i> Event Places
                </a>

                <a href="{{ route('admin.items', ['type' => 'designs']) }}" class="tab-button {{ $type === 'designs' ? 'tab-active' : 'tab-inactive' }}">
                    <i class="fas fa-palette"></i> Event Designs
                </a>

                <a href="{{ route('admin.items.create', ['type' => $type]) }}" class="btn btn-success" style="float:right;">
                    <i class="fas fa-plus"></i> Add New
                </a>

                <div style="clear:both;"></div>
            </div>

            @if ($items->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No items found.
                    <a href="{{ route('admin.items.create', ['type' => $type]) }}">Add your first item!</a>
                </div>
            @else
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            @if ($cfg['has_capacity'])
                                <th>Capacity</th>
                            @endif
                            <th>Price</th>
                            @if ($hasIsAvailable)
                                <th>Status</th>
                            @endif
                            @if ($hasCreatedAt)
                                <th>Created</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            @php
                                $id = $item->{$cfg['pk']};
                                $name = $item->{$cfg['name']};
                                $price = $item->{$cfg['price']};
                                $desc = $item->description ?? '';
                                $available = $hasIsAvailable ? (int)($item->is_available ?? 0) : 1;
                            @endphp
                            <tr>
                                <td>#{{ $id }}</td>
                                <td>
                                    <strong>{{ $name }}</strong><br>
                                    <small>{{ \Illuminate\Support\Str::limit($desc, 50) }}</small>
                                </td>

                                @if ($cfg['has_capacity'])
                                    <td>{{ $item->capacity ?? '-' }} guests</td>
                                @endif

                                <td>
                                    ${{ number_format((float)$price, 2) }}
                                    @if ($type === 'food')
                                        <br><small>per person</small>
                                    @endif
                                </td>

                                @if ($hasIsAvailable)
                                    <td>
                                        <span style="
                                            background-color: {{ $available ? '#28a745' : '#dc3545' }};
                                            color: white;
                                            padding: 0.25rem 0.5rem;
                                            border-radius: 20px;
                                            font-size: 0.85rem;
                                        ">
                                            {{ $available ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </td>
                                @endif

                                @if ($hasCreatedAt)
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}</td>
                                @endif

                                <td>
                                    <div style="display:flex; gap:0.25rem;">
                                        <a href="{{ route('admin.items.edit', ['id' => $id, 'type' => $type]) }}"
                                           class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.items.delete', ['id' => $id, 'type' => $type]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" style="padding: 0.25rem 0.5rem;"
                                                    onclick="return confirm('Delete this item?')">
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