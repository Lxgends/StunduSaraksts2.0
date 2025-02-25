<?php

namespace App\Http\Controllers;

use App\Models\Kabinets;
use Illuminate\Http\Request;

class KabinetsController extends Controller
{
    public function index()
    {
        try {
            $kabinets = Kabinets::all();
    
            if (!$kabinets) {
                return response()->json(['message' => 'No courses found'], 404);
            }

            return response()->json($kabinets);
        } catch (\Exception $e) {
            \Log::error('Error fetching course: ' . $e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
