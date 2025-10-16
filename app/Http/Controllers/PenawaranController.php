<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenawaranController extends Controller
{
    public function index()
    {
        $penawarans = \App\Models\Penawaran::all();
        return view('penawaran.list', compact('penawarans'));
    }

    public function store(Request $request)
    {
        \App\Models\Penawaran::create($request->all());
        return redirect()->route('penawaran.list');
    }

    public function followUp()
    {
        // Halaman Follow Up
        return view('penawaran.followUp');
    }

    public function rekapSurvey()
    {
        // Halaman Rekap Survey
        return view('penawaran.rekapSurvey');
    }
    public function show(Request $request)
    {
        $id = $request->query('id');
        $penawaran = \App\Models\Penawaran::find($id);
        return view('penawaran.detail', compact('penawaran'));
    }

    public function save(Request $request)
    {
        $data = $request->all();

        $penawaranId = $data['penawaran_id'] ?? null;
        $sections = $data['sections'] ?? [];
        $profit = $data['profit'] ?? 0;

        if (!$penawaranId) {
            return response()->json(['error' => 'Penawaran ID tidak ditemukan'], 400);
        }

        \App\Models\PenawaranDetail::where('id_penawaran', $penawaranId)->delete();

        foreach ($sections as $section) {
            $area = $section['area'] ?? null;
            foreach ($section['data'] as $row) {
                \App\Models\PenawaranDetail::create([
                    'id_penawaran' => $penawaranId,
                    'area' => $area,
                    'no' => $row['no'] ?? null,
                    'tipe' => $row['tipe'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'qty' => $row['qty'] ?? null,
                    'satuan' => $row['satuan'] ?? null,
                    'harga_satuan' => $row['harga_satuan'] ?? null,
                    'harga_total' => $row['harga_total'] ?? null,
                    'hpp' => $row['hpp'] ?? null,
                    'profit' => $profit,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
