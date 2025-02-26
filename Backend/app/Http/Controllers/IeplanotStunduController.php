<?php

namespace App\Http\Controllers;

use App\Models\IeplanotStundu;
use Illuminate\Http\Request;

class IeplanotStunduController extends Controller
{
    public function index(Request $request)
    {
        $kurssName = $request->query('kurss');
        $teacherName = $request->query('pasniedzejs');
        $kabinetsNumber = $request->query('kabinets');

        $stundas = IeplanotStundu::with(['kurss', 'pasniedzejs', 'kabinets', 'stunda', 'datums'])
            ->when($kurssName, function ($query) use ($kurssName) {
                $query->whereHas('kurss', function ($query) use ($kurssName) {
                    $query->where('Nosaukums', $kurssName);
                });
            })
            ->when($teacherName, function ($query) use ($teacherName) {
                $query->whereHas('pasniedzejs', function ($query) use ($teacherName) {
                    $names = explode(' ', $teacherName);
                    if (count($names) === 2) {
                        $query->where('Vards', $names[0])->where('Uzvards', $names[1]);
                    }
                });
            })
            ->when($kabinetsNumber, function ($query) use ($kabinetsNumber) {
                $query->whereHas('kabinets', function ($query) use ($kabinetsNumber) {
                    $query->where('Skaitlis', $kabinetsNumber);
                });
            })
            ->get();

        return response()->json($stundas);
    }
}
