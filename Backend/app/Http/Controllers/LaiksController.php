<?php

namespace App\Http\Controllers;

use App\Models\Laiks;
use Illuminate\Http\Request;

class LaiksController extends Controller
{
    public function index()
    {
        try {
            $laiks = Laiks::all();
    
            if ($laiks->isEmpty()) {
                return response()->json(['message' => 'Netika atrasti pārstundu laiki'], 404);
            }

            return response()->json($laiks);
        } catch (\Exception $e) {
            \Log::error('Error fetching courses: ' . $e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
