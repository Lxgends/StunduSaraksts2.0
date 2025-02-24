<?php

namespace App\Http\Controllers;

use App\Models\Kurss;
use Illuminate\Http\Request;

class KurssController extends Controller
{
    public function index()
    {
        try {
            // Try fetching the first course from the database
            $kurss = Kurss::first();
    
            if (!$kurss) {
                return response()->json(['message' => 'No courses found'], 404);
            }
    
            // Return the course data as JSON
            return response()->json($kurss);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error fetching course: ' . $e->getMessage());
    
            // Return a 500 error if something goes wrong
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
}
