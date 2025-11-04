<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Detailparticipant;
use App\Models\Monitoringparticipant;
use App\Models\Transaksi;
use App\Models\UMR;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MonitoringparticipantController extends Controller
{
    public function index(Request $request)
    {
        $title = "Monitoring participant";
        $userRole = auth()->user()->roles[0]->name;
        $umr = UMR::where('is_used', true)->first();
        $tahun = $request->tahun ? $request->tahun : null;
        $cabangId = $request->cabang_id ? $request->cabang_id : null;
        $cabang = Cabang::withTrashed()->get();

        if ($userRole == 'owner' || $userRole == 'pic') {
            $participant = Detailparticipant::query()
                ->join('users as u', 'u.id', '=', 'detail_participant.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select('detail_participant.*', 'c.nama as nama_cabang', 'c.deleted_at as cabang_deleted_at')
                ->get();

            $monitoring = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.*',
                    'dg.nama as  nama_participant',
                    'c.nama as nama_cabang',
                    'c.deleted_at as cabang_deleted_at'
                )
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.tahun', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->orderBy('monitoring_participant.detail_participant_id', 'asc')
                ->get();

            $pendapatanparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.bulan',
                    DB::raw("SUM(monitoring_participant.upah) as pendapatan_participant")
                )
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->groupBy('monitoring_participant.bulan', 'c.id')
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->get()
                ->keyBy('bulan');

            $hasilPendapatanparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilPendapatanparticipant[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($pendapatanparticipant[$bulan]) ? $pendapatanparticipant[$bulan]->pendapatan_participant : 0,
                ];
            }

            $jumlahparticipant = Detailparticipant::query()
                ->join('users as u', 'u.id', '=', 'detail_participant.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    DB::raw('MONTH(detail_participant.created_at) as bulan'),
                    DB::raw("COUNT(detail_participant.id) as jumlah_participant")
                )
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where(DB::raw('YEAR(detail_participant.created_at)'), $tahun);
                })
                ->groupBy(
                    DB::raw('MONTH(detail_participant.created_at)')
                )
                ->get()
                ->keyBy('bulan');

            $hasilJumlahparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilJumlahparticipant[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($jumlahparticipant[$bulan]) ? $jumlahparticipant[$bulan]->jumlah_participant : 0,
                ];
            }

            $statusparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.status',
                    'monitoring_participant.bulan',
                    DB::raw("COUNT(monitoring_participant.detail_participant_id) as jumlah_participant")
                )
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->groupBy(
                    'monitoring_participant.bulan',
                    'monitoring_participant.status',
                    'c.id'
                )
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->get()
                ->keyBy('bulan');

            $hasilStatusparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilStatusparticipant['Lulus'][$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($statusparticipant[$bulan]) ? ($statusparticipant[$bulan]->status == 'Lulus' ? $statusparticipant[$bulan]->jumlah_participant : 0) : 0,
                ];
                $hasilStatusparticipant['participant'][$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($statusparticipant[$bulan]) ? ($statusparticipant[$bulan]->status == 'participant' ? $statusparticipant[$bulan]->jumlah_participant : 0) : 0,
                ];
            }
        } else {
            $participant = Detailparticipant::query()
                ->join('users as u', 'u.id', '=', 'detail_participant.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select('detail_participant.*', 'c.nama as nama_cabang', 'c.deleted_at as cabang_deleted_at')
                ->where('u.cabang_id', auth()->user()->cabang_id)
                ->get();

            $monitoring = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.*',
                    'dg.nama as  nama_participant',
                    'c.nama as nama_cabang',
                    'c.deleted_at as cabang_deleted_at'
                )
                ->where('u.cabang_id', auth()->user()->cabang_id)
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.tahun', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->orderBy('monitoring_participant.detail_participant_id', 'asc')
                ->get();

            $pendapatanparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.bulan',
                    DB::raw("SUM(monitoring_participant.upah) as pendapatan_participant")
                )
                ->where('u.cabang_id', auth()->user()->cabang_id)
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->groupBy(
                    'monitoring_participant.bulan'
                )
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->get()
                ->keyBy('bulan');

            $hasilPendapatanparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilPendapatanparticipant[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($pendapatanparticipant[$bulan]) ? $pendapatanparticipant[$bulan]->pendapatan_participant : 0,
                ];
            }

            $jumlahparticipant = Detailparticipant::query()
                ->join('users as u', 'u.id', '=', 'detail_participant.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    DB::raw('MONTH(detail_participant.created_at) as bulan'),
                    DB::raw("COUNT(detail_participant.id) as jumlah_participant")
                )
                ->where('u.cabang_id', auth()->user()->cabang_id)
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where(DB::raw('YEAR(detail_participant.created_at)'), $tahun);
                })
                ->groupBy(
                    DB::raw('MONTH(detail_participant.created_at)')
                )
                ->get()
                ->keyBy('bulan');

            $hasilJumlahparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilJumlahparticipant[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($jumlahparticipant[$bulan]) ? $jumlahparticipant[$bulan]->jumlah_participant : 0,
                ];
            }

            $statusparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
                ->select(
                    'monitoring_participant.status',
                    'monitoring_participant.bulan',
                    DB::raw("COUNT(monitoring_participant.detail_participant_id) as jumlah_participant")
                )
                ->where('u.cabang_id', auth()->user()->cabang_id)
                ->when($cabangId, function ($query, $cabangId) {
                    return $query->where('c.id', $cabangId);
                })
                ->when($tahun, function ($query, $tahun) {
                    return $query->where('monitoring_participant.tahun', $tahun);
                })
                ->groupBy(
                    'monitoring_participant.bulan',
                    'monitoring_participant.status'
                )
                ->orderBy('c.id', 'asc')
                ->orderBy('monitoring_participant.bulan', 'asc')
                ->get()
                ->keyBy('bulan');

            $hasilStatusparticipant = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $hasilStatusparticipant['Lulus'][$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($statusparticipant[$bulan]) ? ($statusparticipant[$bulan]->status == 'Lulus' ? $statusparticipant[$bulan]->jumlah_participant : 0) : 0,
                ];
                $hasilStatusparticipant['participant'][$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($statusparticipant[$bulan]) ? ($statusparticipant[$bulan]->status == 'participant' ? $statusparticipant[$bulan]->jumlah_participant : 0) : 0,
                ];
            }
        }

        return view('dashboard.monitoring.index', compact('title', 'monitoring', 'umr', 'participant', 'cabang', 'tahun', 'hasilPendapatanparticipant', 'hasilJumlahparticipant', 'hasilStatusparticipant'));
    }

    public function perbaruiDataMonitoring()
    {
        $userRole = auth()->user()->roles[0]->name;
        $umr = UMR::where('is_used', true)->first();

        if ($userRole == 'owner' || $userRole == 'pic') {
            $participant = User::query()
                ->withTrashed()
                ->join('detail_participant as dg', 'dg.user_id', '=', 'users.id')
                ->select('dg.*')
                ->get();

            Monitoringparticipant::query()
                ->where('bulan', Carbon::now()->format('m'))
                ->where('tahun', Carbon::now()->format('Y'))
                ->delete();

            foreach ($participant as $item) {
                $upahparticipant = 0;
                $upahTambahan = Transaksi::query()
                    ->select('total_biaya_layanan_tambahan')
                    ->where('participant_id', $item->id)
                    ->where(DB::raw("MONTH(waktu)"), Carbon::now()->format('m'))
                    ->where(DB::raw("YEAR(waktu)"), Carbon::now()->format('Y'))
                    ->where('status', 'Selesai')
                    ->orderBy('waktu', 'asc')
                    ->sum('total_biaya_layanan_tambahan');

                $monitoring = Transaksi::query()
                    ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
                    ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
                    ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
                    ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
                    ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
                    ->join('detail_participant as dg', 'dg.id', '=', 'transaksi.participant_id')
                    ->select(
                        'dg.nama as nama_participant',
                        DB::raw("SUM(dt.total_pakaian * hjl.harga) as upah_participant"),
                        DB::raw("MONTH(transaksi.waktu) as bulan"),
                        DB::raw("YEAR(transaksi.waktu) as tahun")
                    )
                    ->where('transaksi.participant_id', $item->id)
                    ->where('jl.for_participant', true)
                    ->where(DB::raw("MONTH(transaksi.waktu)"), Carbon::now()->format('m'))
                    ->where(DB::raw("YEAR(transaksi.waktu)"), Carbon::now()->format('Y'))
                    ->where('transaksi.status', 'Selesai')
                    ->groupBy('dg.nama', DB::raw("MONTH(transaksi.waktu)"), DB::raw("YEAR(transaksi.waktu)"))
                    ->orderBy('transaksi.waktu', 'asc')
                    ->first();
                if ($monitoring) {
                    $upahparticipant += $monitoring->upah_participant;
                }
                $upahAkhir = $upahparticipant + $item->pemasukkan + $upahTambahan;

                if ($monitoring) {
                    if ($upahAkhir >= $umr->upah) {
                        Monitoringparticipant::create([
                            'upah' => $upahAkhir,
                            'status' => "Lulus",
                            'bulan' => $monitoring->bulan,
                            'tahun' => $monitoring->tahun,
                            'detail_participant_id' => $item->id,
                        ]);
                    } else {
                        Monitoringparticipant::create([
                            'upah' => $upahAkhir,
                            'status' => "participant",
                            'bulan' => $monitoring->bulan,
                            'tahun' => $monitoring->tahun,
                            'detail_participant_id' => $item->id,
                        ]);
                    }
                } else {
                    Monitoringparticipant::create([
                        'upah' => 0 + $item->pemasukkan,
                        'status' => "participant",
                        'bulan' => Carbon::now()->format('m'),
                        'tahun' => Carbon::now()->format('Y'),
                        'detail_participant_id' => $item->id,
                    ]);
                }
            }
        } else {
            $cabang = Cabang::where('id', auth()->user()->cabang_id)->first();
            $participant = User::query()
                ->withTrashed()
                ->join('detail_participant as dg', 'dg.user_id', '=', 'users.id')
                ->where('users.cabang_id', $cabang->id)
                ->select('dg.*')
                ->get();

            $cabangparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->where('u.cabang_id', $cabang->id)
                ->where('bulan', Carbon::now()->format('m'))
                ->where('tahun', Carbon::now()->format('Y'))
                ->select('dg.nama as nama_participant', 'monitoring_participant.detail_participant_id as participant_id')
                ->get();

            foreach ($cabangparticipant as $item) {
                Monitoringparticipant::query()
                    ->where('detail_participant_id', $item->participant_id)
                    ->where('bulan', Carbon::now()->format('m'))
                    ->where('tahun', Carbon::now()->format('Y'))
                    ->delete();
            }

            foreach ($participant as $item) {
                $upahparticipant = 0;
                $upahTambahan = Transaksi::query()
                    ->select('total_biaya_layanan_tambahan')
                    ->where('participant_id', $item->id)
                    ->where(DB::raw("MONTH(waktu)"), Carbon::now()->format('m'))
                    ->where(DB::raw("YEAR(waktu)"), Carbon::now()->format('Y'))
                    ->where('status', 'Selesai')
                    ->orderBy('waktu', 'asc')
                    ->sum('total_biaya_layanan_tambahan');

                $monitoring = Transaksi::query()
                    ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
                    ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
                    ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
                    ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
                    ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
                    ->join('detail_participant as dg', 'dg.id', '=', 'transaksi.participant_id')
                    ->select('dg.nama as  nama_participant', DB::raw("SUM(dt.total_pakaian * hjl.harga) as upah_participant"), DB::raw("MONTH(transaksi.waktu) as bulan"), DB::raw("YEAR(transaksi.waktu) as tahun"))
                    ->where('transaksi.participant_id', $item->id)
                    ->where('jl.for_participant', true)
                    ->where(DB::raw("MONTH(transaksi.waktu)"), Carbon::now()->format('m'))
                    ->where(DB::raw("YEAR(transaksi.waktu)"), Carbon::now()->format('Y'))
                    ->where('transaksi.status', 'Selesai')
                    ->groupBy('dg.nama', DB::raw("MONTH(transaksi.waktu)"), DB::raw("YEAR(transaksi.waktu)"))
                    ->orderBy('transaksi.waktu', 'asc')
                    ->first();
                if ($monitoring) {
                    $upahparticipant += $monitoring->upah_participant;
                }
                $upahAkhir = $upahparticipant + $item->pemasukkan + $upahTambahan;

                if ($monitoring) {
                    if ($upahAkhir >= $umr->upah) {
                        Monitoringparticipant::create([
                            'upah' => $upahAkhir,
                            'status' => "Lulus",
                            'bulan' => $monitoring->bulan,
                            'tahun' => $monitoring->tahun,
                            'detail_participant_id' => $item->id,
                        ]);
                    } else {
                        Monitoringparticipant::create([
                            'upah' => $upahAkhir,
                            'status' => "participant",
                            'bulan' => $monitoring->bulan,
                            'tahun' => $monitoring->tahun,
                            'detail_participant_id' => $item->id,
                        ]);
                    }
                } else {
                    Monitoringparticipant::create([
                        'upah' => 0 + $item->pemasukkan,
                        'status' => "participant",
                        'bulan' => Carbon::now()->format('m'),
                        'tahun' => Carbon::now()->format('Y'),
                        'detail_participant_id' => $item->id,
                    ]);
                }
            }

            return to_route('monitoring')->with('success', 'Perbarui Data Berhasil Dilakukan');
        }
    }

    public function resetDataMonitoring()
    {
        $userRole = auth()->user()->roles[0]->name;
        $umr = UMR::where('is_used', true)->first();

        if ($userRole == 'owner' || $userRole == 'pic') {
            $participant = User::query()
                ->withTrashed()
                ->join('detail_participant as dg', 'dg.user_id', '=', 'users.id')
                ->select('dg.*')
                ->get();

            Monitoringparticipant::truncate();

            foreach ($participant as $itemparticipant) {
                $upahparticipant = [];

                $upahTambahan = Transaksi::query()
                    ->select(
                        DB::raw("SUM(total_biaya_layanan_tambahan) as total_biaya_layanan_tambahan"),
                        DB::raw("MONTH(waktu) as bulan"),
                        DB::raw("YEAR(waktu) as tahun")
                    )
                    ->where('participant_id', $itemparticipant->id)
                    ->where('status', 'Selesai')
                    ->groupBy(DB::raw("MONTH(waktu)"), DB::raw("YEAR(waktu)"))
                    ->orderBy('waktu', 'asc')
                    ->get();
                foreach ($upahTambahan as $data) {
                    $key = $data->tahun . '-' . $data->bulan;
                    if (!isset($upahparticipant[$key])) {
                        $upahparticipant[$key] = 0;
                    }
                    $upahparticipant[$key] += $data->total_biaya_layanan_tambahan;
                }

                $monitoring = Transaksi::query()
                    ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
                    ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
                    ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
                    ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
                    ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
                    ->join('detail_participant as dg', 'dg.id', '=', 'transaksi.participant_id')
                    ->select('dg.nama as  nama_participant', DB::raw("SUM(dt.total_pakaian * hjl.harga) as upah_participant"), DB::raw("MONTH(transaksi.waktu) as bulan"), DB::raw("YEAR(transaksi.waktu) as tahun"))
                    ->where('transaksi.participant_id', $itemparticipant->id)
                    ->where('jl.for_participant', true)
                    ->where('transaksi.status', 'Selesai')
                    ->groupBy('dg.nama', DB::raw("MONTH(transaksi.waktu)"), DB::raw("YEAR(transaksi.waktu)"))
                    ->orderBy('transaksi.waktu', 'asc')
                    ->get();

                foreach ($monitoring as $data) {
                    $key = $data->tahun . '-' . $data->bulan;
                    if (!isset($upahparticipant[$key])) {
                        $upahparticipant[$key] = 0;
                    }
                    $upahparticipant[$key] += $data->upah_participant;
                }

                if ($monitoring->first()) {
                    foreach ($monitoring as $itemMonitoring) {
                        $key = $itemMonitoring->tahun . '-' . $itemMonitoring->bulan;
                        $totalUpah = $upahparticipant[$key] + $itemparticipant->pemasukkan;

                        if ($totalUpah >= $umr->upah) {
                            Monitoringparticipant::create([
                                'upah' => $totalUpah,
                                'status' => "Lulus",
                                'bulan' => $itemMonitoring->bulan,
                                'tahun' => $itemMonitoring->tahun,
                                'detail_participant_id' => $itemparticipant->id,
                            ]);
                        } else {
                            Monitoringparticipant::create([
                                'upah' => $totalUpah,
                                'status' => "participant",
                                'bulan' => $itemMonitoring->bulan,
                                'tahun' => $itemMonitoring->tahun,
                                'detail_participant_id' => $itemparticipant->id,
                            ]);
                        }
                    }
                } else {
                    Monitoringparticipant::create([
                        'upah' => 0 + $itemparticipant->pemasukkan,
                        'status' => "participant",
                        'bulan' => Carbon::now()->format('m'),
                        'tahun' => Carbon::now()->format('Y'),
                        'detail_participant_id' => $itemparticipant->id,
                    ]);
                }
            }
        } else {
            $cabang = Cabang::where('id', auth()->user()->cabang_id)->first();
            $participant = User::query()
                ->withTrashed()
                ->join('detail_participant as dg', 'dg.user_id', '=', 'users.id')
                ->where('users.cabang_id', $cabang->id)
                ->select('dg.*')
                ->get();

            $cabangparticipant = Monitoringparticipant::query()
                ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
                ->join('users as u', 'u.id', '=', 'dg.user_id')
                ->where('u.cabang_id', $cabang->id)
                ->select('dg.nama as nama_participant', 'monitoring_participant.detail_participant_id as participant_id')
                ->get();

            foreach ($cabangparticipant as $item) {
                Monitoringparticipant::query()
                    ->where('detail_participant_id', $item->participant_id)
                    ->delete();
            }

            foreach ($participant as $itemparticipant) {
                $upahparticipant = [];

                $upahTambahan = Transaksi::query()
                    ->select(
                        DB::raw("SUM(total_biaya_layanan_tambahan) as total_biaya_layanan_tambahan"),
                        DB::raw("MONTH(waktu) as bulan"),
                        DB::raw("YEAR(waktu) as tahun")
                    )
                    ->where('participant_id', $itemparticipant->id)
                    ->where('status', 'Selesai')
                    ->groupBy(DB::raw("MONTH(waktu)"), DB::raw("YEAR(waktu)"))
                    ->orderBy('waktu', 'asc')
                    ->get();
                foreach ($upahTambahan as $data) {
                    $key = $data->tahun . '-' . $data->bulan;
                    if (!isset($upahparticipant[$key])) {
                        $upahparticipant[$key] = 0;
                    }
                    $upahparticipant[$key] += $data->total_biaya_layanan_tambahan;
                }

                $monitoring = Transaksi::query()
                    ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
                    ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
                    ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
                    ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
                    ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
                    ->join('detail_participant as dg', 'dg.id', '=', 'transaksi.participant_id')
                    ->select('dg.nama as  nama_participant', DB::raw("SUM(dt.total_pakaian * hjl.harga) as upah_participant"), DB::raw("MONTH(transaksi.waktu) as bulan"), DB::raw("YEAR(transaksi.waktu) as tahun"))
                    ->where('transaksi.participant_id', $itemparticipant->id)
                    ->where('jl.for_participant', true)
                    ->where('transaksi.status', 'Selesai')
                    ->groupBy('dg.nama', DB::raw("MONTH(transaksi.waktu)"), DB::raw("YEAR(transaksi.waktu)"))
                    ->orderBy('transaksi.waktu', 'asc')
                    ->get();
                foreach ($monitoring as $data) {
                    $key = $data->tahun . '-' . $data->bulan;
                    if (!isset($upahparticipant[$key])) {
                        $upahparticipant[$key] = 0;
                    }
                    $upahparticipant[$key] += $data->upah_participant;
                }

                if ($monitoring->first()) {
                    foreach ($monitoring as $itemMonitoring) {
                        $key = $itemMonitoring->tahun . '-' . $itemMonitoring->bulan;
                        $totalUpah = $upahparticipant[$key] + $itemparticipant->pemasukkan;

                        if ($totalUpah >= $umr->upah) {
                            Monitoringparticipant::create([
                                'upah' => $totalUpah,
                                'status' => "Lulus",
                                'bulan' => $itemMonitoring->bulan,
                                'tahun' => $itemMonitoring->tahun,
                                'detail_participant_id' => $itemparticipant->id,
                            ]);
                        } else {
                            Monitoringparticipant::create([
                                'upah' => $totalUpah,
                                'status' => "participant",
                                'bulan' => $itemMonitoring->bulan,
                                'tahun' => $itemMonitoring->tahun,
                                'detail_participant_id' => $itemparticipant->id,
                            ]);
                        }
                    }
                } else {
                    Monitoringparticipant::create([
                        'upah' => 0 + $itemparticipant->pemasukkan,
                        'status' => "participant",
                        'bulan' => Carbon::now()->format('m'),
                        'tahun' => Carbon::now()->format('Y'),
                        'detail_participant_id' => $itemparticipant->id,
                    ]);
                }
            }

            return to_route('monitoring')->with('success', 'Perbarui Data Berhasil Dilakukan');
        }
    }

    public function editPemasukkan(Request $request)
    {
        $participant = Detailparticipant::find($request->id, ['id', 'nama_pemasukkan', 'pemasukkan']);
        return $participant;
    }

    public function updatePemasukkan(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_pemasukkan' => 'required|string|max:255',
                'pemasukkan' => 'required|decimal:0,2',
            ],
            [
                'required' => ':attribute harus diisi.',
                'max' => ':attribute tidak boleh lebih dari :max karakter.',
                'decimal' => ':attribute tidak boleh lebih dari :max nol dibelakang koma.',
            ]
        );
        $validated = $validator->validated();

        $perbarui = Detailparticipant::where('id', $request->id)->update([
            'nama_pemasukkan' => $validated['nama_pemasukkan'],
            'pemasukkan' => $validated['pemasukkan'],
        ]);

        if ($perbarui) {
            return to_route('monitoring')->with('success', 'participant Berhasil Diperbarui');
        } else {
            return to_route('monitoring')->with('error', 'participant Gagal Diperbarui');
        }
    }

    public function indexRw(Request $request)
    {
        $title = "Monitoring participant";
        $userRole = auth()->user()->roles[0]->name;

        if ($userRole != 'rw') {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $rw = User::query()
            ->join('rw', 'rw.user_id', '=', 'users.id')
            ->where('users.id', auth()->user()->id)
            ->select('rw.nomor_rw as nomor_rw')
            ->first();

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m');
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m');

        $monitoring = Monitoringparticipant::query()
            ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
            ->join('participant as g', 'g.id', '=', 'dg.participant_id')
            ->join('users as u', 'u.id', '=', 'dg.user_id')
            ->where('g.rw', $rw->nomor_rw)
            ->where(function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->where(function ($subQuery) use ($tanggalAwal) {
                    $subQuery->where('tahun', '>', Carbon::parse($tanggalAwal)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAwal) {
                            $nestedQuery->where('tahun', '=', Carbon::parse($tanggalAwal)->format('Y'))
                                ->where('bulan', '>=', Carbon::parse($tanggalAwal)->format('m'));
                        });
                })->where(function ($subQuery) use ($tanggalAkhir) {
                    $subQuery->where('tahun', '<', Carbon::parse($tanggalAkhir)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAkhir) {
                            $nestedQuery->where('tahun', '=', Carbon::parse($tanggalAkhir)->format('Y'))
                                ->where('bulan', '<=', Carbon::parse($tanggalAkhir)->format('m'));
                        });
                });
            })
            ->select('monitoring_participant.*', 'dg.nama as nama_participant', 'dg.pemasukkan as pemasukkan_participant', 'g.rw as nomor_rw')
            ->orderBy('monitoring_participant.tahun', 'asc')
            ->orderBy('monitoring_participant.bulan', 'asc')
            ->orderBy('monitoring_participant.detail_participant_id', 'asc')
            ->get();

        return view('dashboard.monitoring.rw', compact('title', 'monitoring', 'rw'));
    }

    public function pdfMonitoringparticipantRw(Request $request)
    {
        $title = "Monitoring participant";

        $rw = User::query()
            ->join('rw', 'rw.user_id', '=', 'users.id')
            ->where('users.id', auth()->user()->id)
            ->select('rw.nomor_rw as nomor_rw')
            ->first();

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m');
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m');

        $monitoring = Monitoringparticipant::query()
            ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
            ->join('participant as g', 'g.id', '=', 'dg.participant_id')
            ->join('users as u', 'u.id', '=', 'dg.user_id')
            ->where('g.rw', $rw->nomor_rw)
            ->where(function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->where(function ($subQuery) use ($tanggalAwal) {
                    $subQuery->where('tahun', '>', Carbon::parse($tanggalAwal)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAwal) {
                            $nestedQuery->where('tahun', '=', Carbon::parse($tanggalAwal)->format('Y'))
                                ->where('bulan', '>=', Carbon::parse($tanggalAwal)->format('m'));
                        });
                })->where(function ($subQuery) use ($tanggalAkhir) {
                    $subQuery->where('tahun', '<', Carbon::parse($tanggalAkhir)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAkhir) {
                            $nestedQuery->where('tahun', '=', Carbon::parse($tanggalAkhir)->format('Y'))
                                ->where('bulan', '<=', Carbon::parse($tanggalAkhir)->format('m'));
                        });
                });
            })
            ->select('monitoring_participant.*', 'dg.nama as  nama_participant', 'g.rw as nomor_rw')
            ->orderBy('monitoring_participant.tahun', 'asc')
            ->orderBy('monitoring_participant.bulan', 'asc')
            ->orderBy('monitoring_participant.detail_participant_id', 'asc')
            ->get();

        $view = view()->share($title, $monitoring);
        $pdf = Pdf::loadView('dashboard.laporan.pdf.monitoring-participant-rw', [
            'judul' => $title,
            'judulTabel' => $title,
            'monitoring' => $monitoring,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'rw' => $rw,
            'footer' => $title
        ])
            ->setPaper('a4', 'landscape');
        // return $pdf->download();
        return $pdf->stream();
    }

    public function riwayatPendapatan(Request $request)
    {
        $title = "Riwayat Pendapatan participant";
        $umr = UMR::where('is_used', true)->first();
        $participant = $request->participant;
        $tahun = $request->tahun ? $request->tahun : null;

        $monitoring = Monitoringparticipant::query()
            ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
            ->join('users as u', 'u.id', '=', 'dg.user_id')
            ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
            ->where('monitoring_participant.detail_participant_id', $participant)
            ->when($tahun, function ($query, $tahun) {
                return $query->where('monitoring_participant.tahun', $tahun);
            })
            ->select('monitoring_participant.*', 'dg.nama as  nama_participant', 'c.nama as nama_cabang', 'c.deleted_at as cabang_deleted_at', 'c.id as cabang_id')
            ->orderBy('monitoring_participant.tahun', 'asc')
            ->orderBy('monitoring_participant.bulan', 'asc')
            ->get();

        $pendapatanparticipant = Monitoringparticipant::query()
            ->join('detail_participant as dg', 'dg.id', '=', 'monitoring_participant.detail_participant_id')
            ->join('users as u', 'u.id', '=', 'dg.user_id')
            ->join('cabang as c', 'c.id', '=', 'u.cabang_id')
            ->select(
                'monitoring_participant.bulan',
                DB::raw("SUM(monitoring_participant.upah) as pendapatan_participant")
            )
            ->when($tahun, function ($query, $tahun) {
                return $query->where('monitoring_participant.tahun', $tahun);
            })
            ->where('monitoring_participant.detail_participant_id', $participant)
            ->groupBy(
                'monitoring_participant.bulan'
            )
            ->orderBy('c.id', 'asc')
            ->orderBy('monitoring_participant.bulan', 'asc')
            ->get()
            ->keyBy('bulan');

        $riwayatMonitoringparticipant = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $riwayatMonitoringparticipant[$bulan] = [
                'bulan' => $bulan,
                'hasil' => isset($pendapatanparticipant[$bulan]) ? $pendapatanparticipant[$bulan]->pendapatan_participant : 0,
            ];
        }

        return view('dashboard.monitoring.riwayat', compact('title', 'umr', 'monitoring', 'riwayatMonitoringparticipant', 'participant'));
    }
}
