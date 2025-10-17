<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $details = $penawaran ? $penawaran->details()->get() : collect();

        $profit = $details->first()->profit ?? 0;

        $sections = $details->groupBy('area')->map(function ($items, $area) {
            return [
                'area' => $area,
                'data' => $items->map(function ($d) {
                    return [
                        'no' => $d->no,
                        'tipe' => $d->tipe,
                        'deskripsi' => $d->deskripsi,
                        'qty' => $d->qty,
                        'satuan' => $d->satuan,
                        'harga_satuan' => $d->harga_satuan,
                        'harga_total' => $d->harga_total,
                        'hpp' => $d->hpp,
                    ];
                })->toArray()
            ];
        })->values()->toArray();

        return view('penawaran.detail', compact('penawaran', 'sections', 'profit'));
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

        // Ambil semua detail lama
        $existingDetails = \App\Models\PenawaranDetail::where('id_penawaran', $penawaranId)->get()->keyBy(function ($item) {
            return $item->no . '|' . $item->area;
        });

        $newKeys = [];

        foreach ($sections as $section) {
            $area = $section['area'] ?? null;
            foreach ($section['data'] as $row) {
                $key = ($row['no'] ?? '') . '|' . $area;
                $newKeys[] = $key;

                $values = [
                    'tipe' => $row['tipe'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'qty' => $row['qty'] ?? null,
                    'satuan' => $row['satuan'] ?? null,
                    'harga_satuan' => $row['harga_satuan'] ?? null,
                    'harga_total' => $row['harga_total'] ?? null,
                    'hpp' => $row['hpp'] ?? null,
                    'profit' => $profit,
                ];

                // Update kalau sudah ada, kalau belum buat baru
                if (isset($existingDetails[$key])) {
                    $existingDetails[$key]->update($values);
                } else {
                    \App\Models\PenawaranDetail::create(array_merge($values, [
                        'id_penawaran' => $penawaranId,
                        'area' => $area,
                        'no' => $row['no'] ?? null,
                    ]));
                }
            }
        }

        // Hapus data yang tidak ada lagi di input
        \App\Models\PenawaranDetail::where('id_penawaran', $penawaranId)
            ->whereNotIn(DB::raw("CONCAT(no, '|', area)"), $newKeys)
            ->delete();

        return response()->json(['success' => true]);
    }
}
