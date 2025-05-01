<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Data::query();

        // Handle search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('uploader', 'like', "%{$search}%")
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

        $data = $query->paginate(10)->appends($request->query());

        return view('datamanage', compact('data'));
    }

    public function destroy($id)
    {
        try {
            $data = Data::findOrFail($id);
            $data->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('data.management')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('data.management')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}