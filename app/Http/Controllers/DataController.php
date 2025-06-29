<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getVerifiedAggregated(): JsonResponse
    {
        try {
            $aggregatedData = Data::where('verified', true)
                ->select(
                    'lat',
                    'long',
                    DB::raw('SUM(spandukCount) as spandukCount'),
                    DB::raw('MAX(id) as id'), // Get the latest ID
                    DB::raw('MAX(uploader) as uploader'), // Get the latest uploader
                    DB::raw('MAX(created_at) as createdAt'), // Get the latest timestamp
                    DB::raw(value: 'MAX(thoroughfare) as thoroughfare'),
                    DB::raw(value: 'MAX(subLocality) as subLocality'),
                    
                )
                ->groupBy('lat', 'long')
                ->get();

            return response()->json($aggregatedData->map(function($item) {
                // Since we are aggregating, we can't show a single image.
                // You might want to link to a page showing all images for these coordinates.
                return [
                    'id' => $item->id,
                    'uploader' => $item->uploader,
                    'lat' => $item->lat,
                    'long' => $item->long,
                    'thoroughfare' => $item->thoroughfare,
                    'createdAt' => $item->createdAt,
                    
                    'spandukCount' => $item->spandukCount,
                    'image_url' => null // No single image for aggregated data
                ];
            }));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch aggregated data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
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

    public function countUnverified(): JsonResponse
    {
        try {
            $count = Data::where('verified', false)->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to count unverified data',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function index(): JsonResponse
    {
        try {
            $data = Data::where('verified', true)->get();
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

            // Find all data points that match these coordinates
            // Using a small tolerance for floating point comparison
            $data = Data::whereRaw('ABS(`lat` - ?) < 0.000001', [$lat])
                       ->whereRaw('ABS(`long` - ?) < 0.000001', [$long])
                       ->whereRaw(sql: 'verified = 1')
                       ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'error' => 'No data found',
                    'message' => 'No data found for the given coordinates'
                ], 404);
            }

            return response()->json($data->map(function($item) {
                // Format image URL
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
}