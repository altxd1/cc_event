<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package ? 'Edit Package' : 'Add Package' }} - BMW Events Admin</title>
    
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

<div class="container" style="margin-top:1.5rem;">
    <h2>{{ $package ? 'Edit' : 'Add New' }} Package</h2>
    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    @php
        $actionUrl = $package
            ? route('admin.packages.update', ['id' => $package->package_id])
            : route('admin.packages.store');
    @endphp
    <form method="POST" action="{{ $actionUrl }}" class="form-container">
        @csrf
        @if ($package)
            @method('PUT')
        @endif
        <div class="form-group">
            <label>Package Name *</label>
            <input type="text" name="package_name" class="form-control" required
                   value="{{ old('package_name', $package->package_name ?? '') }}">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $package->description ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Price (MAD) *</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required
                   value="{{ old('price', $package->price ?? '') }}">
        </div>
        <div class="text-center">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> {{ $package ? 'Update Package' : 'Add Package' }}
            </button>
            <a class="btn btn-secondary" href="{{ route('admin.packages.index') }}">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>