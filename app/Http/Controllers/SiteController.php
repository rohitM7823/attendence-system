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

    // Get all sites
    public function index()
    {
        $sites = Site::all();
        return response()->json(['message' => 'All sites fetched', 'sites' => $sites], 200);
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
