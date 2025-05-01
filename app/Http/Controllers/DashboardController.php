<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Data::query();

        // Handle search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('uploader', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%")
                  ->orWhere('spandukCount', 'like', "%{$search}%")
                  ->orWhere('thoroughfare', 'like', "%{$search}%")
                  ->orWhere('subLocality', 'like', "%{$search}%")
                  ->orWhere('locality', 'like', "%{$search}%")
                  ->orWhere('subAdmin', 'like', "%{$search}%")
                  ->orWhere('adminArea', 'like', "%{$search}%")
                  ->orWhere('postalCode', 'like', "%{$search}%");
            });
        }

        // Handle sorting
        $sortColumns = ['created_at', 'uploader', 'group', 'spandukCount', 'thoroughfare', 'subLocality', 'locality', 'subAdmin', 'adminArea', 'postalCode'];
        $sort = $request->input('sort');
        $direction = $request->input('direction');

        if ($sort && in_array($sort, $sortColumns)) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $data = $query->paginate(50)->appends($request->query());

        return view('dashboard', compact('data'));
    }
}