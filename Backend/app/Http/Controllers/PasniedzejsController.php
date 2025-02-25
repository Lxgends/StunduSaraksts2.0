<?php

namespace App\Http\Controllers;

use App\Models\Pasniedzejs;
use Illuminate\Http\Request;

class PasniedzejsController extends Controller
{
    public function index()
    {
        try {
            $pasniedzejs = Pasniedzejs::all();
    
            if (!$pasniedzejs) {
                return response()->json(['message' => 'Netika atrasti pasniedzēji'], 404);
            }

            return response()->json($pasniedzejs);
        } catch (\Exception $e) {
            \Log::error('Error fetching course: ' . $e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
