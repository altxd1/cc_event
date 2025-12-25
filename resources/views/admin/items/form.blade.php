<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item ? 'Edit' : 'Add' }} {{ $cfg['title'] }} - Admin</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
<div class="container" style="margin-top: 2rem;">
    <h2>{{ $item ? 'Edit' : 'Add New' }} {{ $cfg['title'] }}</h2>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @php
        $actionUrl = $item
            ? route('admin.items.update', ['id' => $item->{$cfg['pk']}, 'type' => $type])
            : route('admin.items.store', ['type' => $type]);
    @endphp

    <form method="POST" action="{{ $actionUrl }}" class="form-container">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" required
                   value="{{ old('name', $item ? $item->{$cfg['name']} : '') }}">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $item->description ?? '') }}</textarea>
        </div>

        @if ($cfg['has_capacity'])
            <div class="form-group">
                <label>Capacity *</label>
                <input type="number" name="capacity" class="form-control" min="1" required
                       value="{{ old('capacity', $item->capacity ?? '') }}">
            </div>
        @endif

        <div class="form-group">
            <label>{{ $cfg['price_label'] }} *</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required
                   value="{{ old('price', $item ? $item->{$cfg['price']} : '') }}">
        </div>

        @if ($hasIsAvailable)
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_available" value="1"
                        @checked(old('is_available', $item ? (int)($item->is_available ?? 0) : 1) == 1)>
                    Available for selection
                </label>
            </div>
        @endif

        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                {{ $item ? 'Update Item' : 'Add Item' }}
            </button>

            <a href="{{ route('admin.items', ['type' => $type]) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>