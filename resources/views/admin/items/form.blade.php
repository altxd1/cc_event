<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item ? 'Edit' : 'Add' }} {{ $cfg['title'] ?? 'Item' }} - Admin</title>

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

        .img-preview{
            margin-top: 10px;
            width: 240px;
            max-width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,.12);
            display:block;
        }
    </style>
</head>
<body>
@php
    // Supporte les 2 formats de cfg:
    // nouveau: name_col / price_col
    // ancien : name / price
    $pk = $cfg['pk'] ?? null;

    $nameCol = $cfg['name_col'] ?? ($cfg['name'] ?? null);
    $priceCol = $cfg['price_col'] ?? ($cfg['price'] ?? null);

    $hasCapacity = (bool)($cfg['has_capacity'] ?? ($type === 'places'));
    $priceLabel = $cfg['price_label'] ?? ($type === 'food' ? 'Price per Person ($)' : 'Price ($)');
@endphp

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

<div class="container" style="margin-top: 1.5rem;">
    <h2>{{ $item ? 'Edit' : 'Add New' }} {{ $cfg['title'] ?? 'Item' }}</h2>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @php
        $actionUrl = $item
            ? route('admin.items.update', ['id' => $item->{$pk}, 'type' => $type])
            : route('admin.items.store', ['type' => $type]);
    @endphp

    <form method="POST" action="{{ $actionUrl }}" class="form-container" enctype="multipart/form-data">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" required
                   value="{{ old('name', ($item && $nameCol) ? ($item->{$nameCol} ?? '') : '') }}">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $item->description ?? '') }}</textarea>
        </div>

        @if ($hasCapacity)
            <div class="form-group">
                <label>Capacity *</label>
                <input type="number" name="capacity" class="form-control" min="1" required
                       value="{{ old('capacity', $item->capacity ?? '') }}">
            </div>
        @endif

        <div class="form-group">
            <label>{{ $priceLabel }} *</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required
                   value="{{ old('price', ($item && $priceCol) ? ($item->{$priceCol} ?? '') : '') }}">
        </div>

        @if (!empty($hasIsAvailable))
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_available" value="1"
                        @checked(old('is_available', $item ? (int)($item->is_available ?? 0) : 1) == 1)>
                    Available for selection
                </label>
            </div>
        @endif

        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">

            @if ($item && !empty($item->image_path))
                <div>
                    <small>Current image:</small>
                    <img class="img-preview" src="{{ asset('storage/'.$item->image_path) }}" alt="Current image">
                </div>
            @endif
        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> {{ $item ? 'Update Item' : 'Add Item' }}
            </button>

            <a class="btn btn-secondary" href="{{ route('admin.items', ['type' => $type]) }}">
                Cancel
            </a>
        </div>
    </form>
</div>
</body>
</html>