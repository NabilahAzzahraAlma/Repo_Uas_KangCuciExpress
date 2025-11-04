<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kang Cuci Laundry Express</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('img/logo-laundry.png') }}">

    {{-- Tailwind & Icons --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fefefe;
            scroll-behavior: smooth;
        }

        .hero-bg {
            background-image: url('{{ asset('img/home-decor-2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            /* Parallax effect for interactivity */
        }

        .overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4));
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .loading-spinner {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10b981;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="text-gray-800">

    {{-- ===================== NAVIGATION BAR (Tambahan untuk Interaktivitas) ===================== --}}
    <nav
        class="fixed top-0 w-full bg-green-700 bg-opacity-90 backdrop-blur-md z-50 shadow-lg transition-all duration-300">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('img/logo-laundry.png') }}" alt="Logo Kang Cuci Laundry Express"
                        class="h-10 w-10">
                    <div class="text-yellow-300 font-bold text-xl">Kang Cuci Laundry Express</div>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="#promo" class="text-white hover:text-yellow-300 transition">Promo</a>
                    <a href="#layanan" class="text-white hover:text-yellow-300 transition">Layanan</a>
                    <a href="#cekTransaksi" class="text-white hover:text-yellow-300 transition">Cek Status</a>
                    <a href="#kontak" class="text-white hover:text-yellow-300 transition">Kontak</a>
                </div>
                <a href="https://wa.me/6281213917124"
                    class="bg-yellow-400 text-green-800 px-4 py-2 rounded-full font-semibold hover:bg-yellow-300 transition">
                    <i class="ri-whatsapp-line mr-2"></i>Pesan Sekarang
                </a>
            </div>
    </nav>

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="hero-bg relative min-h-screen flex flex-col justify-center items-center text-center pt-20">
        <div class="overlay absolute inset-0"></div>
        <div class="relative z-10 px-5 fade-in">
            <h1 class="text-4xl md:text-6xl font-extrabold text-yellow-300 drop-shadow-lg mb-4">
                Kang Cuci Laundry Express
            </h1>
            <p class="text-white mt-4 text-lg md:text-xl font-medium max-w-2xl mx-auto">
                <span class="text-yellow-300 font-bold text-3xl md:text-4xl">Harga 7000/Kg</span><br>
                Sehari Beres Tanpa Harga Express! Layanan cepat, bersih, dan terpercaya untuk kebutuhan laundry Anda.
            </p>
            <div class="mt-8 flex flex-col md:flex-row gap-4 justify-center">
                <a href="https://wa.me/6281213917124"
                    class="inline-block bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-full text-lg font-semibold shadow-lg transition hover-lift">
                    <i class="ri-whatsapp-line mr-2"></i>Hubungi via WhatsApp
                </a>
                <a href="#cekTransaksi"
                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-green-800 px-8 py-4 rounded-full text-lg font-semibold shadow-lg transition hover-lift">
                    <i class="ri-search-line mr-2"></i>Cek Status Laundry
                </a>
                <a href="{{ route('pelanggan.login') }}"
                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-green-800 px-8 py-4 rounded-full text-lg font-semibold shadow-lg transition hover-lift">
                    <i class="ri-user-line mr-2"></i>Login / Daftar Akun
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== PROMO SERVICE ===================== --}}
    <section id="promo" class="py-20 bg-green-50 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold text-green-700 mb-12 uppercase tracking-wide fade-in">Promo &
                Keunggulan Kami</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover-lift fade-in">
                    <i class="ri-timer-flash-line text-green-500 text-6xl mb-6"></i>
                    <h3 class="font-bold text-xl mb-4">Sehari Beres Tanpa Harga Express</h3>
                    <p class="text-gray-600">Layanan cepat, bersih, dan rapi tanpa biaya tambahan. Kami pastikan pakaian
                        Anda siap dalam waktu singkat.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover-lift fade-in" style="animation-delay: 0.2s;">
                    <i class="ri-motorbike-fill text-yellow-500 text-6xl mb-6"></i>
                    <h3 class="font-bold text-xl mb-4">Gratis Antar Jemput</h3>
                    <p class="text-gray-600">Ambil dan antar pakaian Anda tanpa biaya tambahan di area terjangkau.
                        Kenyamanan Anda prioritas kami.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover-lift fade-in" style="animation-delay: 0.4s;">
                    <i class="ri-sparkling-fill text-blue-500 text-6xl mb-6"></i>
                    <h3 class="font-bold text-xl mb-4">Bersih, Rapih, Wangi</h3>
                    <p class="text-gray-600">Gunakan parfum pilihan sesuai selera pelanggan. Hasil laundry yang
                        memuaskan dan harum semerbak.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== LAYANAN TAMBAHAN ===================== --}}
    <section id="layanan" class="py-20 bg-yellow-100 text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold text-green-800 mb-8 uppercase tracking-wide fade-in">Layanan
                Kami</h2>
            <p class="text-lg text-gray-700 mb-12 max-w-4xl mx-auto fade-in">
                Kami melayani berbagai jenis laundry: <b>Tas</b>, <b>Boneka</b>, <b>Sepatu</b>, <b>Jas</b>, <b>Bed
                    Cover</b>, <b>Gorden</b>,
                <b>Karpet</b>, dan lainnya. Dengan peralatan modern dan tenaga profesional, kami siap memenuhi kebutuhan
                Anda.
            </p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 justify-items-center">
                <div class="bg-white p-6 rounded-xl shadow-md hover-lift transition fade-in">
                    <i class="ri-t-shirt-line text-5xl text-green-600 mb-4"></i>
                    <p class="font-semibold">Pakaian Harian</p>
                    <p class="text-sm text-gray-600">Cuci kering dan setrika</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md hover-lift transition fade-in"
                    style="animation-delay: 0.1s;">
                    <i class="ri-handbag-line text-5xl text-yellow-600 mb-4"></i>
                    <p class="font-semibold">Tas & Boneka</p>
                    <p class="text-sm text-gray-600">Perawatan khusus</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md hover-lift transition fade-in"
                    style="animation-delay: 0.2s;">
                    <i class="ri-home-6-line text-5xl text-green-600 mb-4"></i>
                    <p class="font-semibold">Sepatu & Jas</p>
                    <p class="text-sm text-gray-600">Dry cleaning premium</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md hover-lift transition fade-in"
                    style="animation-delay: 0.3s;">
                    <i class="ri-home-6-line text-5xl text-yellow-600 mb-4"></i>
                    <p class="font-semibold">Gorden & Karpet</p>
                    <p class="text-sm text-gray-600">Pembersihan mendalam</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIAL (Tambahan untuk Interaktivitas) ===================== --}}
    <section class="py-20 bg-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold text-green-700 mb-12 uppercase tracking-wide fade-in">Apa
                Kata Pelanggan Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-green-50 p-6 rounded-2xl shadow-lg hover-lift fade-in">
                    <p class="text-gray-600 italic mb-4">"Laundry cepat dan hasilnya bagus banget! Sudah 3 bulan pake
                        jasa ini."</p>
                    <p class="font-bold text-green-700">- Ibu Siti, Karawang</p>
                </div>
                <div class="bg-green-50 p-6 rounded-2xl shadow-lg hover-lift fade-in" style="animation-delay: 0.2s;">
                    <p class="text-gray-600 italic mb-4">"Gratis antar jemput, sangat membantu. Pakaian wangi dan
                        rapi."
                    </p>
                    <p class="font-bold text-green-700">- Pak Budi, Adiarsa</p>
                </div>
                <div class="bg-green-50 p-6 rounded-2xl shadow-lg hover-lift fade-in" style="animation-delay: 0.4s;">
                    <p class="text-gray-600 italic mb-4">"Harga terjangkau, layanan prima. Recommended!"</p>
                    <p class="font-bold text-green-700">- Mba Rina, Karawang</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CEK STATUS TRANSAKSI ===================== --}}
    <section id="cekTransaksi" class="bg-green-600 py-20 text-center text-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-6 uppercase tracking-wide fade-in">Cek Status Laundry
                Anda</h2>
            <p class="text-yellow-100 mb-12 fade-in">Masukkan nota transaksi Anda untuk melihat status pengerjaan
                laundry secara real-time.</p>

            <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8 text-gray-700">
                <input type="text" name="nota" placeholder="Masukkan Nomor Nota"
                    class="w-full border rounded-lg p-4 mb-6 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                <button type="button" name="cekTransaksi"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-lg transition flex items-center justify-center">
                    <i class="ri-search-line mr-2"></i>Cek Status
                </button>
                <div class="loading-spinner mt-4" id="loadingSpinner"></div>
                <div id="hasilCek" class="mt-6 text-left"></div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer id="kontak" class="bg-green-700 text-white py-12 text-center">
        <div class="container mx-auto px-6">
            <h3 class="text-2xl md:text-3xl font-bold mb-4">Kang Cuci Laundry Express</h3>
            <p class="text-yellow-300 mb-2">Jl. Citarum Krajan No.01, Adiarsa Barat, Karawang</p>
            <p class="mb-4">Instagram:
                <a href="https://instagram.com/laundrykangcuci" target="_blank"
                    class="text-yellow-400 hover:underline transition">@laundrykangcuci</a>
            </p>
            <div class="flex justify-center gap-6 mt-6">
                <a href="https://wa.me/6281213917124"
                    class="bg-green-500 p-4 rounded-full hover:bg-green-600 transition hover-lift">
                    <i class="ri-whatsapp-line text-2xl"></i>
                </a>
                <a href="https://instagram.com/laundrykangcuci"
                    class="bg-yellow-400 p-4 rounded-full hover:bg-yellow-500 transition hover-lift">
                    <i class="ri-instagram-line text-2xl text-green-900"></i>
                </a>
            </div>
            <p class="mt-8 text-sm text-yellow-200">© {{ date('Y') }} Kang Cuci Laundry Express • Bersih, Rapih,
                Wangi</p>
        </div>
    </footer>

    {{-- ===================== SCRIPT CEK TRANSAKSI ===================== --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $("button[name='cekTransaksi']").click(function(e) {
            e.preventDefault();
            const button = $(this);
            const spinner = $("#loadingSpinner");
            const hasilCek = $("#hasilCek");

            // Show loading spinner and disable button
            spinner.show();
            button.prop('disabled', true).text('Mencari...');

            $.ajax({
                type: "get",
                url: "{{ route('landing-page.nota') }}",
                data: {
                    "nota": $("input[name='nota']").val()
                },
                success: function(data) {
                    if (data.length > 0) {
                        hasilCek.html(`
                            <div class="bg-green-100 p-4 rounded-lg border-l-4 border-green-500 fade-in">
                                <p><b>Nota:</b> ${data[0].nota_pelanggan}</p>
                                <p><b>Status:</b> ${data[0].status}</p>
                                <p><b>Total:</b> Rp${data[0].total_bayar_akhir}</p>
                            </div>
                        `);
                    } else {
                        hasilCek.html(
                            `<p class="text-red-500 fade-in">Transaksi tidak ditemukan. Periksa kembali nomor nota Anda.</p>`
                        );
                    }
                },
                error: function() {
                    hasilCek.html(
                        `<p class="text-red-500 fade-in">Terjadi kesalahan koneksi. Coba lagi nanti.</p>`
                    );
                },
                complete: function() {
                    // Hide spinner and re-enable button
                    spinner.hide();
                    button.prop('disabled', false).html(
                        '<i class="ri-search-line mr-2"></i>Cek Status');
                }
            });
        });

        // Smooth scroll for navigation
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });
    </script>

</body>

</html>
