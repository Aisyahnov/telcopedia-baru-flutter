<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function serve($path)
    {
        // Decode path if it contains multiple segments
        $path = str_replace('|', '/', $path);
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        $response->header("Access-Control-Allow-Origin", "*");
        $response->header("Access-Control-Allow-Methods", "GET, OPTIONS");
        $response->header("Access-Control-Allow-Headers", "Origin, Content-Type, Accept, Authorization, X-Requested-With");

        return $response;
    }
}
