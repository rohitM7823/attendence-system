<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Add a site
    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|array',
            'radius' => 'required|numeric',
        ]);

        $site = Site::create([
            'name' => $request->name,
            'location' => $request->location,
            'radius' => $request->radius,
        ]);

        return response()->json(['message' => 'Site added successfully', 'status' => true], 200);
    }

    // Get all sites with pagination and search
public function index(Request $request)
{
    $query = Site::query();

    // Optional search by name
    if ($request->has('search') && $request->search !== null) {
        $search = $request->input('search');
        $query->where('name', 'like', "%$search%");
    }

    // Get pagination parameters
    $perPage = $request->input('per_page', 10);
    $currentPage = $request->input('page', 1);

    // Apply pagination
    $sites = $query->paginate($perPage, ['*'], 'page', $currentPage);

    return response()->json([
        'message' => 'Sites fetched successfully',
        'sites' => $sites->items(),
        'pagination' => [
            'current_page' => $sites->currentPage(),
            'last_page' => $sites->lastPage(),
            'per_page' => $sites->perPage(),
            'total' => $sites->total(),
        ]
    ], 200);
}

    // Update a site
    public function update(Request $request, $id)
    {
        $site = Site::find($id);

        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        $request->validate([
            'name' => 'nullable|string',
            'location' => 'nullable|array',
            'radius' => 'nullable|numeric',
        ]);

        $site->update(array_filter($request->only(['name', 'location', 'radius'])));

        return response()->json(['message' => 'Site updated successfully', 'site' => $site, 'status' => true], 200);
    }

    // Delete a site
    public function delete($id)
    {
        $site = Site::find($id);

        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        $site->delete();
        return response()->json(['message' => 'Site deleted successfully'], 200);
    }
}
