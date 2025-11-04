@extends('layouts.app')

@section('title', 'Pesan Laundry - Kang Cuci Laundry Express')

@section('content')
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="container mx-auto max-w-2xl">
            <h2 class="text-3xl font-bold text-green-700 text-center mb-8">Pesan Laundry Online</h2>
            <form id="orderForm" class="bg-white p-8 rounded-2xl shadow-lg">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Paket Laundry</label>
                    <select name="paket" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" required>
                        <option value="">Pilih Paket</option>
                        <option value="cuci_kering">Cuci Kering (Rp7.000/kg)</option>
                        <option value="dry_clean">Dry Clean (Rp10.000/kg)</option>
                        <option value="setrika">Setrika Saja (Rp5.000/kg)</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Parfum</label>
                    <select name="parfum" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" required>
                        <option value="">Pilih Parfum</option>
                        <option value="lavender">Lavender</option>
                        <option value="citrus">Citrus</option>
                        <option value="ocean">Ocean Breeze</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Jumlah (Kg)</label>
                    <input type="number" name="jumlah_kg" min="1"
                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Opsi Pickup/Delivery</label>
                    <select name="pickup" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" required>
                        <option value="pickup">Pickup di Toko</option>
                        <option value="delivery">Delivery Gratis (Area Terjangkau)</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Alamat (Jika Delivery)</label>
                    <textarea name="alamat" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" rows="3"></textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Catatan Tambahan</label>
                    <textarea name="catatan" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500" rows="2"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold transition flex items-center justify-center">
                    <i class="ri-send-plane-line mr-2"></i>Kirim Pesanan
                </button>
            </form>
            <div id="orderMessage" class="mt-6 text-center"></div>
        </div>
    </div>

    <script>
        $('#orderForm').submit(function(e) {
            e.preventDefault();
            const button = $('button[type="submit"]');
            button.prop('disabled', true).html('<div class="loading-spinner"></div> Mengirim...');

            $.ajax({
                type: 'POST',
                url: '{{ route('pelanggan.order.store') }}',
                data: $(this).serialize(),
                success: function(response) {
                    $('#orderMessage').html(
                        '<p class="text-green-500">Pesanan berhasil dikirim! Status: Menunggu Verifikasi Admin.</p>'
                        );
                    $('#orderForm')[0].reset();
                },
                error: function(xhr) {
                    $('#orderMessage').html('<p class="text-red-500">Gagal mengirim pesanan: ' + xhr
                        .responseJSON.message + '</p>');
                },
                complete: function() {
                    button.prop('disabled', false).html(
                        '<i class="ri-send-plane-line mr-2"></i>Kirim Pesanan');
                }
            });
        });
    </script>
@endsection
