<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Antrian - {{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .kios-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .kios-header img {
            max-height: 100px;
            margin-bottom: 1rem;
        }

        .kios-header h1 {
            font-weight: 700;
            color: #333;
            font-size: 2.5rem;
        }

        .service-grid {
            width: 100%;
            max-width: 1200px;
        }

        .service-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            padding: 2.5rem 2rem;
            text-align: center;
            height: 100%; /* Membuat semua tombol sama tinggi */
        }

        .service-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.1);
        }

        .service-button h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #0d6efd;
            margin: 0;
        }
        
        .wait-time-info {
            font-size: 0.95rem;
            color: #666;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            border-top: 1px solid #eee;
        }
        
        .wait-time-info i {
            color: #ff6b6b;
            font-weight: 600;
        }

        /* Area Cetak Tersembunyi
          Ini adalah struk yang akan dicetak.
        */
        #print-area {
            display: none;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
            width: 280px; /* Ukuran umum kertas thermal 80mm */
            font-size: 10pt;
            line-height: 1.4;
        }

        #print-area h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
        }
        #print-area p {
            margin: 5px 0;
        }
        #print-area h1 {
            font-size: 28pt;
            font-weight: bold;
            margin: 10px 0;
        }
        #print-area img {
            max-width: 180px; /* QR Code jangan terlalu besar */
            margin-top: 10px;
        }
        #print-area .footer {
            margin-top: 15px;
            font-style: italic;
        }


        /* CSS @media print
          Saat `window.print()` dipanggil, sembunyikan semua elemen KECUALI #print-area
        */
        @media print {
            body * {
                visibility: hidden; /* Sembunyikan semuanya */
            }

            #print-area,
            #print-area * {
                visibility: visible; /* Tampilkan hanya area cetak */
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="kios-header px-3">
        @if ($pengaturan && $pengaturan->logo)
            <img src="{{ asset('logo/' . $pengaturan->logo) }}" alt="Logo Instansi">
        @endif
        <h1>{{ $pengaturan->nama_instansi ?? 'Selamat Datang' }}</h1>
        <p class="lead">Silakan pilih layanan untuk mengambil nomor antrian.</p>
    </div>

    <div class="container-fluid service-grid px-md-5">
        <div class="row justify-content-center g-4">
            @forelse ($layanans as $layanan)
                <div class="col-12 col-md-6 col-lg-4">
                    <button class="service-button" data-id="{{ $layanan->id }}">
                        <div style="width: 100%;">
                            <h2>{{ $layanan->nama_layanan }}</h2>
                            <div class="wait-time-info">
                                <i class="fas fa-hourglass-half"></i> 
                                <span id="wait-{{ $layanan->id }}">Menghitung...</span>
                            </div>
                        </div>
                    </button>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        Saat ini tidak ada layanan yang tersedia.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div id="print-area"></div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Load wait times saat halaman dimuat dan update setiap 10 detik
        function loadWaitTimes() {
            fetch('{{ route('kios.wait-times') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(layanan => {
                        const elem = document.getElementById(`wait-${layanan.layanan_id}`);
                        if (elem) {
                            elem.textContent = layanan.formatted;
                        }
                    });
                })
                .catch(err => {
                    console.log('Error loading wait times:', err);
                    // Jika gagal, tetap tampilkan "Menghitung..." dan retry nanti
                });
        }
        
        // Load saat DOM ready dan refresh setiap 10 detik
        document.addEventListener('DOMContentLoaded', function() {
            loadWaitTimes();
            setInterval(loadWaitTimes, 10000);
            
            const serviceButtons = document.querySelectorAll('.service-button');

            serviceButtons.forEach(button => {
                button.addEventListener('click', handleServiceClick);
            });

            async function handleServiceClick(event) {
                const layananId = event.currentTarget.dataset.id;
                
                // 1. Tampilkan notifikasi "Loading"
                Swal.fire({
                    title: 'Mencetak Antrian...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // 2. Kirim request ke KiosController@cetak
                    // Rute ini 'kios.cetak' sudah di-set tanpa CSRF di web.php Anda
                    const response = await axios.post('{{ route('kios.cetak') }}', {
                        layanan_id: layananId
                    });

                    if (response.data.success) {
                        // 3. Jika sukses, panggil fungsi untuk mengisi data cetak
                        populatePrintArea(response.data);
                        
                        // 4. Tutup "Loading" dan panggil dialog cetak
                        Swal.close();
                        window.print();

                    } else {
                        // Tampilkan error jika success: false
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.data.message || 'Terjadi kesalahan.'
                        });
                    }

                } catch (error) {
                    // 5. Tangani error (misal: server down, validasi gagal, 500)
                    console.error('Error saat mencetak:', error);
                    let errorMessage = 'Gagal terhubung ke server.';
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mencetak',
                        text: errorMessage
                    });
                }
            }

            function populatePrintArea(data) {
                const printDiv = document.getElementById('print-area');
                const { antrian, layanan, pengaturan, qr_code } = data;

                // Format Waktu Ambil
                const waktuAmbil = new Date(antrian.waktu_ambil).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // 6. Buat HTML untuk struk
                let html = `
                    <h3>${pengaturan.nama_instansi}</h3>
                    <p>${pengaturan.alamat}</p>
                    <hr>
                    <p>Nomor Antrian Anda:</p>
                    <h1>${antrian.kode_antrian}</h1>
                    <p>Layanan: <strong>${layanan.nama_layanan}</strong></p>
                    <p>Waktu: ${waktuAmbil}</p>
                `;

                // Tambahkan QR Code jika ada (controller Anda sudah menangani ini)
                if (qr_code) {
                    html += `<img src="${qr_code}" alt="Scan untuk status antrian">`;
                }

                html += `<p class="footer">Terima kasih atas kunjungan Anda. <br> Silakan tunggu nomor Anda dipanggil.</p>`;

                // 7. Masukkan HTML ke div cetak
                printDiv.innerHTML = html;
            }

        });
    </script>

</body>
</html>