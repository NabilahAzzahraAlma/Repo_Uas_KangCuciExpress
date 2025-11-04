<?php

namespace App\Http\Controllers;

use App\Http\Requests\participant\participantRequest;
use App\Imports\participantImport;
use App\Models\Detailparticipant;
use App\Models\participant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class participantController extends Controller
{
    public function index()
    {
        $title = "participant";
        $participant = participant::orderBy('created_at', 'asc')->get();
        return view('dashboard.participant.index', compact('title', 'participant'));
    }

    public function store(participantRequest $request)
    {
        $validated = $request->validated();
        $tambah = participant::create($validated);
        if ($tambah) {
            return to_route('participant')->with('success', 'participant Berhasil Ditambahkan');
        } else {
            return to_route('participant')->with('error', 'participant Gagal Ditambahkan');
        }
    }

    public function show(Request $request)
    {
        $participant = participant::findOrFail($request->id);
        return $participant;
    }

    public function edit(Request $request)
    {
        $participant = participant::find($request->id);
        return $participant;
    }

    public function update(participantRequest $request)
    {
        $validated = $request->validated();
        $perbarui = participant::where('id', $request->id)->update($validated);
        if ($perbarui) {
            return to_route('participant')->with('success', 'participant Berhasil Diperbarui');
        } else {
            return to_route('participant')->with('error', 'participant Gagal Diperbarui');
        }
    }

    public function delete(Request $request)
    {
        $hapus = participant::where('id', $request->id)->delete();
        if ($hapus) {
            abort(200, 'participant Berhasil Dihapus');
        } else {
            abort(400, 'participant Gagal Dihapus');
        }
    }

    public function anggota(Request $request)
    {
        $title = "Anggota Keluarga";
        $participant = participant::where('kartu_keluarga', $request->detail_participant)->orderBy('created_at', 'asc')->first();
        $detailparticipant = Detailparticipant::where('participant_id', $participant->id)->orderBy('created_at', 'asc')->get();
        return view('dashboard.participant.anggota', compact('title', 'participant', 'detailparticipant'));
    }

    public function detailAnggota(Request $request)
    {
        $detailparticipant = Detailparticipant::where('id', $request->id)->orderBy('created_at', 'asc')->first();
        $detailparticipant['tanggal_lahir'] = Carbon::parse($detailparticipant['tanggal_lahir'])->format('d F Y');
        return $detailparticipant;
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new participantImport, $request->file('impor'));
            return to_route('participant')->with('success', 'participant Berhasil Ditambahkan');
        } catch (\Exception $ex) {
            Log::info($ex);
            return to_route('participant')->with('error', 'participant Gagal Ditambahkan');
        }
    }
}
