<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AdminItemsController extends Controller
{
    private function typeFromRequest(Request $request): string
    {
        $type = $request->query('type', 'food');

        if (! in_array($type, ['food', 'places', 'designs'], true)) {
            abort(404);
        }

        return $type;
    }

    private function cfg(string $type): array
    {
        return match ($type) {
            'food' => [
                'type' => 'food',
                'title' => 'Food Items',
                'table' => 'food_items',
                'pk' => 'food_id',
                'name' => 'food_name',
                'price' => 'price_per_person',
                'has_capacity' => false,
                // Use MAD to indicate Moroccan Dirhams rather than US dollars
                'price_label' => 'Price per Person (MAD)',
            ],
            'places' => [
                'type' => 'places',
                'title' => 'Event Places',
                'table' => 'event_places',
                'pk' => 'place_id',
                'name' => 'place_name',
                'price' => 'price',
                'has_capacity' => true,
                'price_label' => 'Price (MAD)',
            ],
            'designs' => [
                'type' => 'designs',
                'title' => 'Event Designs',
                'table' => 'event_designs',
                'pk' => 'design_id',
                'name' => 'design_name',
                'price' => 'price',
                'has_capacity' => false,
                'price_label' => 'Price (MAD)',
            ],
            default => abort(404),
        };
    }

    public function index(Request $request)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        $hasCreatedAt = Schema::hasColumn($cfg['table'], 'created_at');
        $hasIsAvailable = Schema::hasColumn($cfg['table'], 'is_available');

        $orderCol = $hasCreatedAt ? 'created_at' : $cfg['pk'];

        $items = DB::table($cfg['table'])
            ->orderByDesc($orderCol)
            ->get();

        return view('admin.items.index', compact('type', 'cfg', 'items', 'hasCreatedAt', 'hasIsAvailable'));
    }

    public function create(Request $request)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        $hasIsAvailable = Schema::hasColumn($cfg['table'], 'is_available');

        return view('admin.items.form', [
            'type' => $type,
            'cfg' => $cfg,
            'item' => null,
            'hasIsAvailable' => $hasIsAvailable,
        ]);
    }

    public function store(Request $request)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            // Allow optional image upload; must be an image file up to 2MB
            'image' => ['nullable', 'image', 'max:2048'],
        ];

        if ($cfg['has_capacity']) {
            $rules['capacity'] = ['required', 'integer', 'min:1', 'max:100000'];
        }

        $data = $request->validate($rules); // https://laravel.com/docs/validation

        $insert = [
            $cfg['name'] => $data['name'],
            'description' => $data['description'] ?? '',
            $cfg['price'] => $data['price'],
        ];

        // Handle image upload if an image column exists and a file was provided
        $imageColumn = null;
        if (Schema::hasColumn($cfg['table'], 'image_url')) {
            $imageColumn = 'image_url';
        } elseif (Schema::hasColumn($cfg['table'], 'image_path')) {
            $imageColumn = 'image_path';
        }
        if ($imageColumn && $request->hasFile('image')) {
            // Determine the correct folder based on item type
            $folder = match ($type) {
                'food' => 'foods',
                'places' => 'venues',
                'designs' => 'designs',
                default => 'items',
            };
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            $destinationPath = public_path('images/' . $folder);
            // Ensure the destination directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            // Move the uploaded file into the public images directory so it can be accessed via asset()
            $file->move($destinationPath, $filename);
            $relative = 'images/' . $folder . '/' . $filename;
            $insert[$imageColumn] = $relative;
        }

        if ($cfg['has_capacity']) {
            $insert['capacity'] = $data['capacity'];
        }

        if (Schema::hasColumn($cfg['table'], 'is_available')) {
            $insert['is_available'] = $request->has('is_available') ? 1 : 0;
        }

        if (Schema::hasColumn($cfg['table'], 'created_at')) {
            $insert['created_at'] = now();
        }
        if (Schema::hasColumn($cfg['table'], 'updated_at')) {
            $insert['updated_at'] = now();
        }

        DB::table($cfg['table'])->insert($insert); // https://laravel.com/docs/queries

        return redirect()->route('admin.items', ['type' => $type])
            ->with('message', 'Item added successfully!');
    }

    public function edit(Request $request, int $id)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        $hasIsAvailable = Schema::hasColumn($cfg['table'], 'is_available');

        $item = DB::table($cfg['table'])->where($cfg['pk'], $id)->first();
        if (! $item) abort(404);

        return view('admin.items.form', compact('type', 'cfg', 'item', 'hasIsAvailable'));
    }

    public function update(Request $request, int $id)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            // Allow optional new image on update
            'image' => ['nullable', 'image', 'max:2048'],
            // Checkbox indicating the admin wants to remove the existing image
            'remove_image' => ['nullable', 'boolean'],
        ];

        if ($cfg['has_capacity']) {
            $rules['capacity'] = ['required', 'integer', 'min:1', 'max:100000'];
        }

        $data = $request->validate($rules);

        $update = [
            $cfg['name'] => $data['name'],
            'description' => $data['description'] ?? '',
            $cfg['price'] => $data['price'],
        ];

        // Determine if the table supports an image column
        $imageColumn = null;
        if (Schema::hasColumn($cfg['table'], 'image_url')) {
            $imageColumn = 'image_url';
        } elseif (Schema::hasColumn($cfg['table'], 'image_path')) {
            $imageColumn = 'image_path';
        }

        // Fetch the current item to retrieve existing image path if needed
        $existingItem = DB::table($cfg['table'])->where($cfg['pk'], $id)->first();

        // Handle removal of existing image if requested
        if ($imageColumn && $request->boolean('remove_image')) {
            // Delete the file from disk if it exists
            $currentPath = $existingItem->{$imageColumn} ?? null;
            if ($currentPath) {
                // Build full path relative to public
                $fullPath = public_path($currentPath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            // Set the image column to null in the update
            $update[$imageColumn] = null;
        }

        // If a new image is uploaded, process it similarly to store()
        if ($imageColumn && $request->hasFile('image')) {
            // If there is an existing image on disk, remove it before replacing
            $currentPath = $existingItem->{$imageColumn} ?? null;
            if ($currentPath) {
                $fullPath = public_path($currentPath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $folder = match ($type) {
                'food' => 'foods',
                'places' => 'venues',
                'designs' => 'designs',
                default => 'items',
            };
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            $destinationPath = public_path('images/' . $folder);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $relative = 'images/' . $folder . '/' . $filename;
            $update[$imageColumn] = $relative;
        }

        if ($cfg['has_capacity']) {
            $update['capacity'] = $data['capacity'];
        }

        if (Schema::hasColumn($cfg['table'], 'is_available')) {
            $update['is_available'] = $request->has('is_available') ? 1 : 0;
        }

        if (Schema::hasColumn($cfg['table'], 'updated_at')) {
            $update['updated_at'] = now();
        }

        DB::table($cfg['table'])->where($cfg['pk'], $id)->update($update);

        return redirect()->route('admin.items', ['type' => $type])
            ->with('message', 'Item updated successfully!');
    }

    public function destroy(Request $request, int $id)
    {
        $type = $this->typeFromRequest($request);
        $cfg = $this->cfg($type);

        DB::table($cfg['table'])->where($cfg['pk'], $id)->delete();

        return redirect()->route('admin.items', ['type' => $type])
            ->with('message', 'Item deleted successfully!');
    }
}