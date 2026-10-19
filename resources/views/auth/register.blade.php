<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register | FeedbackRise</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 46% 54%;
            padding: 0;
            gap: 0;
        }

        /* Panel kiri (gambar) */
        .art-panel {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 24px 0 24px 24px;
            overflow: hidden;
        }
        .brand-art {
            display: block;
            width: auto;
            height: calc(100vh - 48px);
            max-width: 100%;
            object-fit: contain;
        }

        /* Panel kanan */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .form-wrap { width: 100%; max-width: 472px; }
        .form-wrap h1 {
            margin: 0 0 14px;
            font-size: 44px;
            line-height: 1.1;
            font-weight: 600;
            letter-spacing: -1px;
        }
        .subtitle {
            margin: 0 0 30px;
            font-size: 18px;
            line-height: 1.35;
        }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 17px;
            font-weight: 500;
        }
        .field input {
            width: 100%;
            height: 56px;
            padding: 0 26px;
            border: 2px solid transparent;
            border-radius: 28px;
            background: #E0E0E0;
            font-family: inherit;
            font-size: 15px;
            color: #000;
            outline: none;
            transition: border-color .15s, background .15s;
        }
        .field input::placeholder { color: #8a8a8a; }
        .field input:focus { background: #fff; border-color: #234FC1; }
        .field .error {
            margin: 6px 0 0 8px;
            font-size: 13px;
            color: #c0392b;
        }

        .btn {
            width: 100%;
            height: 56px;
            margin-top: 24px;
            border: 1px solid #1d3f9e;
            border-radius: 28px;
            background: #234FC1;
            color: #fff;
            font-family: inherit;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        .btn:hover { background: #1d43a6; }
        .btn:focus-visible { outline: 3px solid #FFD55C; outline-offset: 2px; }

        .switch {
            margin-top: 22px;
            text-align: center;
            font-size: 14px;
        }
        .switch a { color: #3a5bd9; text-decoration: none; }
        .switch a:hover { text-decoration: underline; }

        /* Responsif */
        @media (max-width: 900px) {
            .page { grid-template-columns: 1fr; }
            .art-panel { justify-content: center; padding: 20px; }
            .brand-art { width: 100%; height: auto; max-width: 500px; }
            .form-panel { padding: 32px 20px; }
            .form-wrap h1 { font-size: 36px; }
        }
    </style>
</head>
<body>
    <div class="page">

        <section class="art-panel" aria-label="FeedbackRise">
            <img
                class="brand-art"
                src="{{ asset('images/feedbackrise-panel.png') }}"
                alt="Speed up your work with our Web App, dengan maskot FeedbackRise"
            >
        </section>

        <main class="form-panel">
            <div class="form-wrap">
                <h1>Create an account</h1>
                <p class="subtitle">Create your account to access your analytics dashboard.</p>

                <form method="POST" action="{{ url('/register') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter Email" autocomplete="email" required>
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Enter Username" autocomplete="username" required>
                        @error('username') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="Enter Password" autocomplete="new-password" required>
                        @error('password') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Enter Password" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn">Sign Up</button>
                </form>

                <p class="switch">Already have an account? <a href="{{ url('/login') }}">Login Here</a></p>
            </div>
        </main>

    </div>
</body>
</html>