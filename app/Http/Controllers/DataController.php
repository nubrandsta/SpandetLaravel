<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
    public function show($id): JsonResponse
    {
        $data = Data::findOrFail($id);
        
        return response()->json([
            'uploader' => $data->uploader,
            'lat' => $data->lat,
            'long' => $data->long,
            'thoroughfare' => $data->thoroughfare,
            'subLocality' => $data->subLocality,
            'locality' => $data->locality,
            'subAdmin' => $data->subAdmin,
            'adminArea' => $data->adminArea,
            'postalCode' => $data->postalCode,
            'createdAt' => $data->created_at->toDateTimeString(),
            'image_url' => $data->imgURI
        ]);
    }
}