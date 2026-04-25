<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Telcopedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menggunakan font Poppins agar mirip dengan referensi -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #9F1521 0%, #4a0910 100%);
        }
        
        .main-card {
            display: flex;
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 500px;
        }

        /* Panel Kiri */
        .left-panel {
            background-color: #f8f9fa;
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .brand-title {
            font-size: 32px;
            font-weight: 800;
            color: #2b2b2b;
            line-height: 1.2;
            margin-bottom: 5px;
        }
        .brand-highlight {
            color: #9F1521;
        }
        .brand-divider {
            height: 4px;
            width: 40px;
            background-color: #9F1521;
            border-radius: 2px;
            margin: 20px 0;
        }
        .brand-desc {
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .avatar-group {
            display: flex;
            align-items: center;
        }
        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -10px;
            background-color: #ef476f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
        }
        .avatar:first-child { margin-left: 0; }
        .avatar.img-avatar { background: none; }
        .active-users-text {
            margin-left: 15px;
            font-size: 10px;
            font-weight: 700;
            color: #495057;
        }
        .active-users-sub {
            color: #f77f00;
            display: block;
            margin-top: 2px;
        }

        /* Panel Kanan (Form) */
        .right-panel {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .form-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #9F1521;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 35px;
        }
        
        .form-group-custom {
            margin-bottom: 20px;
        }
        .label-custom {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #8d99ae;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .input-custom {
            width: 100%;
            background-color: #fbfbfb;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            transition: all 0.2s;
        }
        .input-custom:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #9F1521;
            box-shadow: 0 0 0 4px rgba(159, 21, 33, 0.1);
        }
        .input-custom::placeholder {
            color: #ced4da;
            font-weight: 400;
        }
        
        .link-forgot {
            display: block;
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: #9F1521;
            text-transform: uppercase;
            text-decoration: none;
            margin-top: -10px;
            margin-bottom: 25px;
        }
        .link-forgot:hover { color: #7c111b; text-decoration: underline; }

        .btn-submit {
            width: 100%;
            background: linear-gradient(90deg, #9F1521 0%, #7c111b 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(159, 21, 33, 0.3);
        }

        .bottom-text {
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
            margin-top: 25px;
        }
        .bottom-link {
            color: #9F1521;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
        }
        
        .copyright {
            position: absolute;
            bottom: 30px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            color: #6c757d;
            text-transform: uppercase;
        }

        @media (max-width: 900px) {
            .main-card {
                flex-direction: column;
                margin: 20px;
                width: auto;
            }
            .left-panel, .right-panel {
                width: 100%;
                padding: 40px 30px;
            }
            .left-panel { min-height: 250px; }
        }
    </style>
</head>
<body>

    <div class="main-card">
        <!-- Panel Kiri (Branding) -->
        <div class="left-panel">
            <div class="brand-title">
                Telcopedia <span class="brand-highlight">x</span><br>
                Mahasiswa Telkom
            </div>
            <div class="brand-divider"></div>
            <div class="brand-desc">
                Platform management jual beli barang preloved yang ringkas. Gaya bertemu performa.
            </div>
        </div>

        <!-- Panel Kanan (Form) -->
        <div class="right-panel">
            <div class="form-title">Log In</div>
            <div class="form-subtitle">Access Your Account</div>

            @if(session('error'))
                <div class="alert alert-danger" style="font-size:12px; padding:10px; border-radius:8px;">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" style="font-size:12px; padding:10px; border-radius:8px;">{{ session('success') }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group-custom">
                    <label class="label-custom">Email</label>
                    <input type="email" name="email" class="input-custom" placeholder="user@mail.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">NIM</label>
                    <input type="text" name="nim" class="input-custom" placeholder="130123..." value="{{ old('nim') }}" required>
                </div>

                <div class="form-group-custom" style="margin-bottom: 12px;">
                    <label class="label-custom">Password</label>
                    <input type="password" name="password" class="input-custom" placeholder="••••••••" required>
                </div>

                <a href="#" class="link-forgot">Lupa Password?</a>

                <button type="submit" class="btn-submit">Log In</button>
            </form>

            <div class="bottom-text">
                New user? <a href="{{ route('register.form') }}" class="bottom-link">Sign Up</a>
            </div>
        </div>
    </div>

    <div class="copyright">
        COPYRIGHT © 2026 - TELCOPEDIA X MAHASISWA TELKOM
    </div>

</body>
</html>
