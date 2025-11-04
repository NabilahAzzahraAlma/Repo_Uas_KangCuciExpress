<table>
    <tr>
        <th>Nota</th>
        <th>Layanan</th>
        <th>Status</th>
        <th>Tanggal</th>
    </tr>
    @foreach ($transaksi as $t)
        <tr>
            <td>{{ $t->nota_layanan }}</td>
            <td>{{ $t->layanan->nama }}</td>
            <td>{{ $t->status }}</td>
            <td>{{ $t->waktu->format('d-m-Y') }}</td>
        </tr>
    @endforeach
</table>
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if ($transaksi->isEmpty())
    <p>Belum ada transaksi.</p>
@endif
