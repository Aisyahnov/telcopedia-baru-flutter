<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Telcopedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #9F1521 0%, #4a0910 100%);
            padding-top: 40px;
            padding-bottom: 40px;
        }
        
        .main-card {
            display: flex;
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            min-height: 550px;
        }

        .left-panel {
            background-color: #f8f9fa; 
            width: 45%;
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
        .brand-highlight { color: #9F1521; }
        .brand-divider {
            height: 4px; width: 40px; background-color: #9F1521;
            border-radius: 2px; margin: 20px 0;
        }
        .brand-desc {
            color: #6c757d; font-weight: 500; font-size: 14px;
            line-height: 1.6; margin-bottom: 40px;
        }
        
        .avatar-group { display: flex; align-items: center; }
        .avatar {
            width: 35px; height: 35px; border-radius: 50%;
            border: 2px solid #fff; margin-left: -10px;
            background-color: #ef476f; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600;
        }
        .avatar:first-child { margin-left: 0; }
        .active-users-text {
            margin-left: 15px; font-size: 10px; font-weight: 700; color: #495057;
        }
        .active-users-sub { color: #f77f00; display: block; margin-top: 2px; }

        .right-panel {
            width: 55%;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-title {
            font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;
        }
        .form-subtitle {
            font-size: 11px; font-weight: 700; color: #9F1521;
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 15px;
        }

        .form-group-custom { margin-bottom: 15px; }
        .label-custom {
            display: block; font-size: 11px; font-weight: 700;
            color: #8d99ae; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .input-custom {
            width: 100%; background-color: #fbfbfb; border: 1px solid transparent;
            border-radius: 12px; padding: 10px 16px;
            font-size: 13px; font-weight: 500; color: #333; transition: all 0.2s;
        }
        select.input-custom { appearance: none; cursor: pointer; }
        .input-custom:focus {
            outline: none; background-color: #ffffff; border-color: #9F1521;
            box-shadow: 0 0 0 4px rgba(159, 21, 33, 0.1);
        }
        .input-custom::placeholder { color: #ced4da; font-weight: 400; }
        
        .btn-submit {
            width: 100%; background: linear-gradient(90deg, #9F1521 0%, #7c111b 100%);
            color: white; border: none; border-radius: 12px;
            padding: 14px; font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s; margin-top: 15px;
        }
        .btn-submit:hover {
            transform: translateY(-2px); box-shadow: 0 8px 20px rgba(159, 21, 33, 0.3);
        }

        .bottom-text {
            text-align: center; font-size: 12px; font-weight: 500;
            color: #6c757d; margin-top: 25px;
        }
        .bottom-link { color: #9F1521; font-weight: 700; text-transform: uppercase; text-decoration: none; }
        
        .copyright {
            margin-top: 30px; font-size: 11px; font-weight: 600;
            letter-spacing: 2px; color: #6c757d; text-transform: uppercase;
        }

        @media (max-width: 900px) {
            .main-card { flex-direction: column; margin: 20px; width: auto; }
            .left-panel, .right-panel { width: 100%; padding: 40px 30px; }
            .left-panel { min-height: 250px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="main-card">
        <!-- Panel Kiri -->
        <div class="left-panel">
            <div class="brand-title">
                Telcopedia <span class="brand-highlight">x</span><br>
                Mahasiswa Telkom
            </div>
            <div class="brand-divider"></div>
            <div class="brand-desc">
                Bergabung bersama kami dan nikmati ekosistem jual beli kampus.
            </div>
        </div>

        <!-- Panel Kanan -->
        <div class="right-panel">
            <div class="form-title">Sign Up</div>
            <div class="form-subtitle">Register Your Account</div>

            @if($errors->any())
                <div class="alert alert-danger" style="font-size:12px; padding:10px; border-radius:8px;">
                    <ul class="mb-0" style="padding-left:15px">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="form-group-custom">
                        <label class="label-custom">Name</label>
                        <input type="text" name="name" class="input-custom" placeholder="Full name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group-custom">
                        <label class="label-custom">Email</label>
                        <input type="email" name="email" class="input-custom" placeholder="user@mail.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group-custom">
                        <label class="label-custom">NIM</label>
                        <input type="text" name="nim" class="input-custom" placeholder="130123..." value="{{ old('nim') }}" required>
                    </div>
                    <div class="form-group-custom">
                        <label class="label-custom">WhatsApp</label>
                        <input type="text" name="whatsapp_number" class="input-custom" placeholder="08..." value="{{ old('whatsapp_number') }}" required>
                    </div>
                    <div class="form-group-custom">
                        <label class="label-custom">Password</label>
                        <input type="password" name="password" class="input-custom" placeholder="••••••••" required>
                    </div>
                    <div class="form-group-custom">
                        <label class="label-custom">Role</label>
                        <select name="role" class="input-custom" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="buyer">Pembeli</option>
                            <option value="seller">Penjual</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign Up</button>
            </form>

            <div class="bottom-text">
                Already have an account? <a href="{{ route('login.form') }}" class="bottom-link">Log In</a>
            </div>
        </div>
    </div>

    <div class="copyright">
        COPYRIGHT © 2026 - TELCOPEDIA X MAHASISWA TELKOM
    </div>

</body>
</html>
