<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
    public function show($id): JsonResponse
    {
        $data = Data::findOrFail($id);
        
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
            'image_url' => $data->imgURI
        ]);
    }
    
    public function index(): JsonResponse
    {
        $data = Data::all();
        return response()->json($data->map(function($item) {
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
                'image_url' => $item->imgURI
            ];
        }));
    }
}