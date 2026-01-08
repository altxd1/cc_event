<?php

namespace App\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPackagesController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index()
    {
        // If the packages table does not exist (e.g. migrations have not
        // been run yet), avoid querying and return an empty collection.
        if (!Schema::hasTable('packages')) {
            $packages = collect();
        } else {
            $packages = Package::orderByDesc('created_at')->get();
        }
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create()
    {
        return view('admin.packages.form', ['package' => null]);
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
        Package::create($data);
        return redirect()->route('admin.packages.index')->with('message', 'Package added successfully!');
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(int $id)
    {
        $package = Package::findOrFail($id);
        return view('admin.packages.form', compact('package'));
    }

    /**
     * Update the specified package in storage.
     */
    public function update(Request $request, int $id)
    {
        $package = Package::findOrFail($id);
        $data = $request->validate([
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
        $package->update($data);
        return redirect()->route('admin.packages.index')->with('message', 'Package updated successfully!');
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(int $id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return redirect()->route('admin.packages.index')->with('message', 'Package deleted successfully!');
    }
}