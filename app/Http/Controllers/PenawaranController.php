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
}
