<?php

namespace App\Http\Controllers;

use App\Models\IeplanotStundu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IeplanotStunduController extends Controller
{
    public function index(Request $request)
    {
        $kurssName = $request->query('kurss');
        $teacherName = $request->query('pasniedzejs');
        $kabinetsNumber = $request->query('kabinets');
        $datumsID = $request->query('datumsID');

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
            ->when($datumsID, function ($query) use ($datumsID) {
                $query->where('datumsID', $datumsID);
            })
            ->get();

        return response()->json($stundas);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'skaitlis' => 'required|integer',
            'kurssID' => 'required|exists:kursi,id',
            'laiksID' => 'required|exists:laiks,id',
            'pasniedzejsID' => 'required|exists:pasniedzejs,id',
            'kabinetaID' => 'required|exists:kabinets,id',
            'datumsID' => 'required|exists:datums,id',
            'stundaID' => 'required|exists:stunda,id',
            Rule::unique('ieplanot_stundas')->where(function ($query) use ($request) {
                return $query->where('skaitlis', $request->skaitlis)
                    ->where('kurssID', $request->kurssID)
                    ->where('laiksID', $request->laiksID)
                    ->where('pasniedzejsID', $request->pasniedzejsID)
                    ->where('kabinetaID', $request->kabinetaID);
            }),
        ]);

        $ieplanotStundu = IeplanotStundu::create($validatedData);

        return response()->json($ieplanotStundu, 201);
    }

    public function update(Request $request, IeplanotStundu $ieplanotStundu)
    {
        $validatedData = $request->validate([
            'skaitlis' => 'required|integer',
            'kurssID' => 'required|exists:kursi,id',
            'laiksID' => 'required|exists:laiks,id',
            'pasniedzejsID' => 'required|exists:pasniedzejs,id',
            'kabinetaID' => 'required|exists:kabinets,id',
            'datumsID' => 'required|exists:datums,id',
            'stundaID' => 'required|exists:stunda,id',
            Rule::unique('ieplanot_stundas')->where(function ($query) use ($request, $ieplanotStundu) {
                return $query->where('skaitlis', $request->skaitlis)
                    ->where('kurssID', $request->kurssID)
                    ->where('laiksID', $request->laiksID)
                    ->where('pasniedzejsID', $request->pasniedzejsID)
                    ->where('kabinetaID', $request->kabinetaID)
                    ->where('id', '!=', $ieplanotStundu->id);
            }),
        ]);

        $ieplanotStundu->update($validatedData);

        return response()->json($ieplanotStundu);
    }
}
