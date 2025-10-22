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

        $sections = $details->groupBy(function ($item) {
            return $item->area . '|' . $item->nama_section;
        })->map(function ($items, $key) {
            [$area, $nama_section] = explode('|', $key);
            return [
                'area' => $area,
                'nama_section' => $nama_section,
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
        $ppnPersen = $data['ppn_persen'] ?? 11; // Default 11%

        if (!$penawaranId) {
            return response()->json(['error' => 'Penawaran ID tidak ditemukan'], 400);
        }

        // Ambil semua detail lama
        $existingDetails = \App\Models\PenawaranDetail::where('id_penawaran', $penawaranId)
            ->get()
            ->keyBy(function ($item) {
                return $item->no . '|' . $item->area;
            });

        $newKeys = [];
        $totalKeseluruhan = 0;

        foreach ($sections as $section) {
            $area = $section['area'] ?? null;
            $namaSection = $section['nama_section'] ?? null;

            foreach ($section['data'] as $row) {
                $key = ($row['no'] ?? '') . '|' . $area;
                $newKeys[] = $key;

                $hargaTotal = $row['harga_total'] ?? 0;
                $totalKeseluruhan += $hargaTotal;

                $values = [
                    'tipe' => $row['tipe'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'qty' => $row['qty'] ?? null,
                    'satuan' => $row['satuan'] ?? null,
                    'harga_satuan' => $row['harga_satuan'] ?? null,
                    'harga_total' => $hargaTotal,
                    'hpp' => $row['hpp'] ?? null,
                    'profit' => $profit,
                    'nama_section' => $namaSection,
                ];

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

        // Hapus data yang tidak ada lagi
        \App\Models\PenawaranDetail::where('id_penawaran', $penawaranId)
            ->whereNotIn(DB::raw("CONCAT(no, '|', area)"), $newKeys)
            ->delete();

        $isBest = !empty($data['is_best_price']) ? 1 : 0;
        $bestPrice = isset($data['best_price']) ? floatval($data['best_price']) : 0;
        $baseAmount = $isBest ? $bestPrice : $totalKeseluruhan;

        // Hitung PPN dan Grand Total
        $ppnNominal = ($baseAmount * $ppnPersen) / 100;
        $grandTotal = $baseAmount + $ppnNominal;

        // Update penawaran dengan total, ppn, dan grand total
        \App\Models\Penawaran::where('id_penawaran', $penawaranId)->update([
            'total' => $totalKeseluruhan,
            'ppn_persen' => $ppnPersen,
            'ppn_nominal' => $ppnNominal,
            'grand_total' => $grandTotal,
            'is_best_price' => $isBest,
            'best_price' => $bestPrice
        ]);

        return response()->json([
            'success' => true,
            'total' => $totalKeseluruhan,
            'base_amount' => $baseAmount,
            'ppn_nominal' => $ppnNominal,
            'grand_total' => $grandTotal
        ]);
    }
}
