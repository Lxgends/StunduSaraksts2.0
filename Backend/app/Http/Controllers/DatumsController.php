<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Datums;

class DatumsController extends Controller
{
    public function index()
    {
        // Fetch all datums
        $datums = Datums::all();
        return response()->json($datums);
    }

    public function show($id)
    {
        // Fetch a specific datums by ID
        $datums = Datums::find($id);
        if ($datums) {
            return response()->json($datums);
        } else {
            return response()->json(['error' => 'Datums not found'], 404);
        }
    }
}
