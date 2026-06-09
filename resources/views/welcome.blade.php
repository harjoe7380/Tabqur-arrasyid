<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('dkm_name', config('app.name', 'Tabungan Qurban')) }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Scripts / Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }
        /* Ornamen Lingkaran Latar Belakang */
        body::before {
            content: "";
            position: absolute;
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(26, 188, 156, 0.2), rgba(41, 128, 185, 0.2));
            filter: blur(40px);
            z-index: 0;
        }
        body::after {
            content: "";
            position: fixed;
            bottom: -150px;
            right: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(243, 156, 18, 0.15), rgba(231, 76, 60, 0.15));
            filter: blur(50px);
            z-index: 0;
        }

        .linktree-container {
            position: relative;
            z-index: 1;
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            padding: 5px;
        }

        .brand-title {
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            text-align: center;
        }

        .brand-subtitle {
            color: #576574;
            font-size: 1rem;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .link-btn {
            display: block;
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 50px;
            padding: 15px 20px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            color: #2c3e50;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        .link-btn:hover {
            transform: translateY(-3px);
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            color: #1abc9c;
        }

        .link-btn.btn-primary-custom {
            background: linear-gradient(120deg, #1abc9c, #16a085);
            color: white;
            border: none;
        }
        .link-btn.btn-primary-custom:hover {
            box-shadow: 0 8px 25px rgba(26, 188, 156, 0.3);
            color: white;
        }

        /* Modal Customization */
        .modal-content {
            border-radius: 20px;
            border: none;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e8f8f5;
            color: #16a085;
        }
    </style>
</head>
<body>
    <div class="linktree-container">
        <!-- Logos -->
        <div class="logo-container">
            @if(\App\Models\Setting::get('logo_tabqur'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('logo_tabqur')) }}" alt="Logo Tabqur" class="logo-img">
            @endif
            @if(\App\Models\Setting::get('logo_dkm'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('logo_dkm')) }}" alt="Logo DKM" class="logo-img">
            @endif
            @if(!\App\Models\Setting::get('logo_tabqur') && !\App\Models\Setting::get('logo_dkm'))
                <div class="logo-img d-flex align-items-center justify-content-center bg-success text-white fw-bold fs-3">
                    {{ substr(\App\Models\Setting::get('dkm_name', config('app.name')), 0, 1) }}
                </div>
            @endif
        </div>

        <h1 class="brand-title">{{ \App\Models\Setting::get('dkm_name', config('app.name', 'Tabungan Qurban')) }}</h1>
        <p class="brand-subtitle">Sistem Manajemen Kurban Digital Terpadu</p>

        <!-- Links -->
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="link-btn btn-primary-custom">Masuk Dashboard Admin</a>
            @else
                <a href="{{ route('peserta.dashboard') }}" class="link-btn btn-primary-custom">Masuk Dashboard</a>
            @endif
        @else
            <a href="{{ route('register') }}" class="link-btn btn-primary-custom">Daftar Tabungan Kurban</a>
            <a href="{{ route('login') }}" class="link-btn">Login ke Akun Anda</a>
        @endauth
        
        @php
            $adminPhone = \App\Models\Setting::get('admin_phone', '08111251918');
            // format phone number for whatsapp link
            if(substr($adminPhone, 0, 1) == '0') {
                $adminPhone = '62' . substr($adminPhone, 1);
            }
        @endphp
        <a href="https://wa.me/{{ $adminPhone }}" target="_blank" class="link-btn">Hubungi Kami (WhatsApp)</a>
        
        <button type="button" class="link-btn" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">
            Konfirmasi Setoran
        </button>

        <button type="button" class="link-btn" data-bs-toggle="modal" data-bs-target="#faqModal">
            FAQ (Tanya Jawab)
        </button>
    </div>

    <!-- FAQ Modal -->
    <div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-success text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <h5 class="modal-title fw-bold" id="faqModalLabel">Tanya Jawab (FAQ)</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            
            <div class="accordion" id="accordionFAQ">
              <!-- FAQ Item 1 -->
              <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button collapsed fw-bold rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    1. Apa itu Sistem Tabungan Kurban?
                  </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">
                    Aplikasi ini adalah platform digital untuk memudahkan Jamaah menabung dana kurban secara bertahap. DKM Masjid dapat memantau uang masuk, dan Jamaah bisa melihat progres tabungannya kapan saja.
                  </div>
                </div>
              </div>

              <!-- FAQ Item 2 -->
              <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                <h2 class="accordion-header" id="headingTwo">
                  <button class="accordion-button collapsed fw-bold rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    2. Bagaimana cara mendaftar?
                  </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">
                    Anda dapat menekan tombol <strong>Daftar Tabungan Kurban</strong> di halaman utama, isi data diri, target harga hewan kurban, dan nomor WhatsApp. Setelah itu, akun Anda akan dibuatkan dan Anda bisa langsung login.
                  </div>
                </div>
              </div>

              <!-- FAQ Item 3 -->
              <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                <h2 class="accordion-header" id="headingThree">
                  <button class="accordion-button collapsed fw-bold rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    3. Ke mana saya harus transfer uang tabungan?
                  </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">
                    Anda bisa mentransfer uang ke rekening resmi DKM yaitu: <br>
                    <strong>{{ \App\Models\Setting::get('bank_account', 'BSI 1234567890 a.n DKM Masjid') }}</strong>.<br>
                    Setelah transfer, segera konfirmasi melalui tombol "Hubungi Kami" di halaman depan agar Admin dapat mencatat setoran Anda di sistem.
                  </div>
                </div>
              </div>

              <!-- FAQ Item 4 -->
              <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                <h2 class="accordion-header" id="headingFour">
                  <button class="accordion-button collapsed fw-bold rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    4. Apakah ada notifikasi ketika uang saya sudah dicatat?
                  </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">
                    Ya, setiap kali Admin memverifikasi dan mencatat uang masuk dari Anda, Anda akan menerima laporan otomatis melalui pesan WhatsApp.
                  </div>
                </div>
              </div>

            </div>

          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Setoran -->
    <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <h5 class="modal-title fw-bold" id="konfirmasiModalLabel">Pilih Jalur Konfirmasi</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <p class="mb-4">Bagaimana Anda ingin mengonfirmasi setoran Anda?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-3 fw-bold rounded-pill p-3">
                📱 Login ke Aplikasi<br>
                <small class="fw-normal">Lewat menu "Lapor Setoran" di Dasbor Anda.</small>
            </a>
            <a href="https://wa.me/{{ $adminPhone }}?text={{ urlencode('Assalamu\'alaikum Admin, saya ingin mengonfirmasi setoran tabungan kurban saya.') }}" target="_blank" class="btn btn-outline-success w-100 fw-bold rounded-pill p-3">
                💬 Konfirmasi via WhatsApp<br>
                <small class="fw-normal">Hubungi admin untuk verifikasi manual.</small>
            </a>
          </div>
        </div>
      </div>
    </div>
</body>
</html>
