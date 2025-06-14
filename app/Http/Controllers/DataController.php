<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
    public function show($id): JsonResponse
    {
        try {
            $data = Data::findOrFail($id);
            
            // Ensure image_url is properly formatted
            $imageUrl = $data->imgURI;
            if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = 'https://spandet.my.id/' . ltrim($imageUrl, '/');
            }
            
            return response()->json([
                'id' => $data->id,
                'uploader' => $data->uploader,
                'group' => $data->group,
                'lat' => $data->lat,
                'long' => $data->long,
                'thoroughfare' => $data->thoroughfare,
                'subLocality' => $data->subLocality,
                'locality' => $data->locality,
                'subAdmin' => $data->subAdmin,
                'adminArea' => $data->adminArea,
                'postalCode' => $data->postalCode,
                'createdAt' => $data->created_at->format('d M Y H:i:s'),
                'spandukCount' => $data->spandukCount,
                'image_url' => $imageUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index(): JsonResponse
    {
        try {
            $data = Data::all();
            return response()->json($data->map(function($item) {
                // Ensure image_url is properly formatted
                $imageUrl = $item->imgURI;
                if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = 'https://spandet.my.id/' . ltrim($imageUrl, '/');
                }
                
                return [
                    'id' => $item->id,
                    'uploader' => $item->uploader,
                    'group' => $item->group,
                    'lat' => $item->lat,
                    'long' => $item->long,
                    'thoroughfare' => $item->thoroughfare,
                    'subLocality' => $item->subLocality,
                    'locality' => $item->locality,
                    'subAdmin' => $item->subAdmin,
                    'adminArea' => $item->adminArea,
                    'postalCode' => $item->postalCode,
                    'createdAt' => $item->created_at->format('d M Y H:i:s'),
                    'spandukCount' => $item->spandukCount,
                    'image_url' => $imageUrl
                ];
            }));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function findByCoordinates(Request $request): JsonResponse
    {
        try {
            $lat = $request->query('lat');
            $long = $request->query('long');

            if (!$lat || !$long) {
                return response()->json([
                    'error' => 'Missing coordinates',
                    'message' => 'Latitude and longitude are required'
                ], 400);
            }

            // Convert coordinates to float for precise comparison
            $lat = (float) $lat;
            $long = (float) $long;

            // Find the first data point that matches these coordinates
            // Using a small tolerance for floating point comparison
            $data = Data::whereRaw('ABS(lat - ?) < 0.000001', [$lat])
                       ->whereRaw('ABS(long - ?) < 0.000001', [$long])
                       ->first();

            if (!$data) {
                return response()->json([
                    'error' => 'No data found',
                    'message' => 'No data found for the given coordinates'
                ], 404);
            }

            // Format image URL
            $imageUrl = $data->imgURI;
            if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = 'https://spandet.my.id/' . ltrim($imageUrl, '/');
            }

            return response()->json([
                'id' => $data->id,
                'uploader' => $data->uploader,
                'group' => $data->group,
                'lat' => $data->lat,
                'long' => $data->long,
                'thoroughfare' => $data->thoroughfare,
                'subLocality' => $data->subLocality,
                'locality' => $data->locality,
                'subAdmin' => $data->subAdmin,
                'adminArea' => $data->adminArea,
                'postalCode' => $data->postalCode,
                'createdAt' => $data->created_at->format('d M Y H:i:s'),
                'spandukCount' => $data->spandukCount,
                'image_url' => $imageUrl
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error in findByCoordinates: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}