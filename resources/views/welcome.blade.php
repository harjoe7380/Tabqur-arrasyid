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
        }
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            position: relative;
            overflow: hidden;
        }
        /* Ornamen Lingkaran Latar Belakang */
        .hero-section::before {
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
        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -150px;
            right: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(243, 156, 18, 0.15), rgba(231, 76, 60, 0.15));
            filter: blur(50px);
            z-index: 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            padding: 4rem 2rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
            z-index: 1;
            position: relative;
        }
        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12);
        }
        .brand-title {
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1.2rem;
            font-size: 3rem;
            background: linear-gradient(120deg, #1abc9c, #2980b9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        .brand-subtitle {
            color: #576574;
            font-size: 1.15rem;
            margin-bottom: 3rem;
            line-height: 1.6;
        }
        .btn-custom {
            border-radius: 50px;
            padding: 14px 36px;
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-login {
            background: linear-gradient(to right, #1abc9c, #16a085);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(26, 188, 156, 0.3);
        }
        .btn-login:hover {
            background: linear-gradient(to right, #16a085, #1abc9c);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(26, 188, 156, 0.4);
        }
        @media (max-width: 768px) {
            .brand-title { font-size: 2.2rem; }
            .glass-card { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="glass-card">
            <h1 class="brand-title">{{ \App\Models\Setting::get('dkm_name', config('app.name', 'Tabungan Qurban')) }}</h1>
            <p class="brand-subtitle">Sistem Manajemen Kurban Digital Terpadu untuk kemudahan transaksi DKM dan Jamaah.</p>
            
            <div class="d-flex justify-content-center gap-3">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-custom btn-login">Masuk Dashboard</a>
                    @else
                        <a href="{{ route('peserta.dashboard') }}" class="btn btn-custom btn-login">Masuk Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-custom btn-login">Login ke Akun Anda</a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
