<?php

namespace App\Http\Controllers;

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
                'price_label' => 'Price per Person ($)',
            ],
            'places' => [
                'type' => 'places',
                'title' => 'Event Places',
                'table' => 'event_places',
                'pk' => 'place_id',
                'name' => 'place_name',
                'price' => 'price',
                'has_capacity' => true,
                'price_label' => 'Price ($)',
            ],
            'designs' => [
                'type' => 'designs',
                'title' => 'Event Designs',
                'table' => 'event_designs',
                'pk' => 'design_id',
                'name' => 'design_name',
                'price' => 'price',
                'has_capacity' => false,
                'price_label' => 'Price ($)',
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