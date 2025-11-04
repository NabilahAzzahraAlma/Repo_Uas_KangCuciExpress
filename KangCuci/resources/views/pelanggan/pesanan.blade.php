<form action="{{ route('pesanan') }}" method="POST">
    @csrf
    <label>Jenis Layanan:</label>
    <select name="layanan_id">
        @foreach ($layanan as $l)
            <option value="{{ $l->id }}">{{ $l->nama }}</option>
        @endforeach
    </select>

    <label>Jumlah Pakaian:</label>
    <input type="number" name="jumlah_pakaian" required>

    <button type="submit">Pesan</button>
</form>
@if ($errors->any())
    <div class="errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
