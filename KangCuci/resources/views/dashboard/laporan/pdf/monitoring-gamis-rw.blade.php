@extends('dashboard.laporan.layouts.main')

@section('tanggal')
    <p style="padding-bottom: 0px">Tanggal: <span style="font-weight: 500">{{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</span></p>
    <p style="padding-bottom: 20px">RW: <span style="font-weight: 500">{{ $rw->nomor_rw }}</span></p>
@endsection

@section('tabel')
    <table>
        <thead>
            <tr>
                <th>participant</th>
                <th>Status</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Upah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monitoring as $value => $item)
                <tr>
                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                            {{ $item->nama_participant }}
                        </p>
                    </td>
                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                            @if ($item->status == 'participant')
                                <span class="badge badge-primary">{{ $item->status }}</span>
                            @elseif ($item->status == 'Lulus')
                                <span class="badge badge-accent">{{ $item->status }}</span>
                            @endif
                        </p>
                    </td>
                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                            {{ $item->bulan }}
                        </p>
                    </td>
                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                            {{ $item->tahun }}
                        </p>
                    </td>
                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                            Rp{{ number_format($item->upah, 2, ',', '.') }}
                        </p>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td></td>
                <td>
                    <span>
                        Status (participant):
                    </span>
                    <span style="font-weight: 500">
                        {{ $monitoring->where('status', 'participant')->count() }} orang
                    </span>
                </td>
                <td></td>
                <td></td>
                <td>
                    <div>Total Pendapatan participant:</div>
                    <div style="font-weight: 500">Rp{{ number_format($monitoring->sum('upah'), 2, ',', '.') }}</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <span>
                        Status (Lulus):
                    </span>
                    <span style="font-weight: 500">
                        {{ $monitoring->where('status', 'Lulus')->count() }} orang
                    </span>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endsection
