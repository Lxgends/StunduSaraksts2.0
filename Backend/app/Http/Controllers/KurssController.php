<?php

namespace App\Http\Controllers;

use App\Models\Kurss;
use Illuminate\Http\Request;

class KurssController extends Controller
{
    public function index()
    {
        try {
            $kurss = Kurss::all();
    
            if ($kurss->isEmpty()) {
                return response()->json(['message' => 'Netika atrasti kursi'], 404);
            }

            return response()->json($kurss);
        } catch (\Exception $e) {
            \Log::error('Error fetching courses: ' . $e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
