<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jasa;
use App\Models\JasaDetail;
use Illuminate\Support\Facades\Log;

class JasaController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->all();
        Log::debug('JasaController::save payload', $data);

        $penawaranId = $data['penawaran_id'] ?? null;
        $sections = $data['sections'] ?? [];
        $profitPercent = floatval($data['profit'] ?? 0);
        $pphPercent = floatval($data['pph'] ?? 0);

        if (!$penawaranId) {
            return response()->json(['error' => 'penawaran_id required'], 400);
        }

        DB::beginTransaction();
        try {
            // hitung total dari semua section/row
            $totalAwal = 0;
            foreach ($sections as $section) {
                foreach ($section['data'] as $row) {
                    $totalAwal += floatval($row['total'] ?? 0);
                }
            }

            // formula inverse (sama dengan view)
            $afterProfit = $profitPercent > 0 ? ($totalAwal / (1 - ($profitPercent / 100))) : $totalAwal;
            $afterPph    = $pphPercent > 0 ? ($afterProfit / (1 - ($pphPercent / 100))) : $afterProfit;

            // SAVE: store the "after" values (nominal totals as shown in view)
            $profitValueToStore = round($afterProfit, 2); // store afterProfit (not difference)
            $pphValueToStore    = round($afterPph, 2);    // store afterPph (not difference)
            $grandTotal         = round($afterPph, 2);

            // create header jasa (DO NOT include total_awal if you don't want to use it)
            $jasa = Jasa::create([
                'id_penawaran'   => $penawaranId,
                // 'total_awal'  => $totalAwal, // removed as requested
                'profit_percent' => $profitPercent,
                'profit_value'   => $profitValueToStore,
                'pph_percent'    => $pphPercent,
                'pph_value'      => $pphValueToStore,
                'bpjsk_percent'  => 0,
                'bpjsk_value'    => 0,
                'grand_total'    => $grandTotal,
            ]);

            Log::debug('Created Jasa header', ['id_jasa' => $jasa->id_jasa, 'jasa' => $jasa->toArray()]);

            // create details (unchanged)
            foreach ($sections as $section) {
                $namaSection = $section['nama_section'] ?? null;
                foreach ($section['data'] as $row) {
                    JasaDetail::create([
                        'id_penawaran'  => $penawaranId,
                        'id_jasa'       => $jasa->id_jasa,
                        'nama_section'  => $namaSection,
                        'no'            => $row['no'] ?? null,
                        'deskripsi'     => $row['deskripsi'] ?? null,
                        'vol'           => $row['vol'] ?? 0,
                        'hari'          => $row['hari'] ?? 0,
                        'orang'         => $row['orang'] ?? 0,
                        'unit'          => $row['unit'] ?? 0,
                        'total'         => $row['total'] ?? 0,
                        'profit'        => $profitPercent,
                        'pph'           => $pphPercent,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id_jasa' => $jasa->id_jasa,
                'total_awal' => $totalAwal,
                'profit_percent' => $profitPercent,
                'profit_value' => $profitValueToStore,
                'pph_percent' => $pphPercent,
                'pph_value' => $pphValueToStore,
                'grand_total' => $grandTotal
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Jasa save error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'payload' => $data]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
