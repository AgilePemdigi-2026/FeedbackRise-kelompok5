<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register | FeedbackRise</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        * { box-sizing: border-box; }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f6f6;
            color: #111;
            font-family: 'Nunito Sans', Arial, sans-serif;
        }

        /* Halaman utama */
        .page {
            width: 100%;
            height: 100vh;
            display: grid;
            grid-template-columns: 46% 54%;
            background: #fff;
            overflow: hidden;
        }

        /* Panel gambar */
        .art-panel {
            width: 100%;
            height: 100%;
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

        /* Panel form */
        .form-panel {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 6% 40px 5%;
        }

        .form {
            width: 100%;
            max-width: 440px;
        }

        /* Judul */
        h1 {
            margin: 0 0 10px;
            font-size: clamp(32px, 3vw, 44px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -0.8px;
        }

        .subtitle {
            margin: 0 0 32px;
            color: #555;
            font-size: clamp(14px, 1.15vw, 17px);
            line-height: 1.5;
        }

        /* Field */
        .field { margin: 0 0 18px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: clamp(12px, 0.9vw, 14px);
            line-height: 1.2;
            font-weight: 800;
        }

        /* Input */
        input {
            width: 100%;
            height: 48px;
            padding: 0 18px;
            border: 0;
            border-radius: 24px;
            outline: none;
            background: #e5e5e5;
            color: #222;
            font: 600 13px 'Nunito Sans', Arial, sans-serif;
        }

        input::placeholder { color: #858585; }

        input:focus { box-shadow: 0 0 0 2px #2854c5; }

        /* Pesan error dan sukses (diisi oleh JS) */
        .error,
        .success {
            margin: 0 0 12px 4px;
            font-size: 13px;
            line-height: 1.4;
        }

        .error { color: #d93025; }
        .success { color: #1e8e3e; }

        /* Tombol Sign Up */
        .submit {
            width: 100%;
            height: 48px;
            margin-top: 10px;
            border: 0;
            border-radius: 24px;
            background: #4054c6;
            color: #fff;
            font: 800 14px 'Nunito Sans', Arial, sans-serif;
            cursor: pointer;
        }

        .submit:hover { background: #3347b5; }

        .submit:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        /* Link ke login */
        .signup {
            margin: 22px 0 0;
            text-align: center;
            color: #666;
            font-size: 11px;
        }

        .signup a {
            color: #2854c5;
            font-weight: 800;
            text-decoration: none;
        }

        .signup a:hover { text-decoration: underline; }

        /* Laptop kecil */
        @media (max-width: 1200px) {
            .page { grid-template-columns: 45% 55%; }
            .art-panel { padding-left: 20px; }
            .form-panel { padding-left: 4%; padding-right: 6%; }
            .form { max-width: 400px; }
        }

        /* Laptop dengan tinggi layar lebih kecil */
        @media (max-height: 750px) and (min-width: 701px) {
            .art-panel { padding-top: 18px; padding-bottom: 18px; }
            .brand-art { height: calc(100vh - 36px); }
            .form-panel { padding-top: 25px; padding-bottom: 25px; }
            h1 { font-size: 34px; }
            .subtitle { margin-bottom: 22px; font-size: 15px; }
            .field { margin-bottom: 12px; }
            input { height: 42px; }
            .submit { height: 42px; }
        }

        /* Tablet */
        @media (max-width: 700px) {
            body { min-height: 100vh; }

            .page {
                width: 100%;
                height: auto;
                min-height: 100vh;
                display: grid;
                grid-template-columns: 1fr;
            }

            .art-panel {
                height: auto;
                justify-content: center;
                padding: 20px;
            }

            .brand-art {
                width: 100%;
                height: auto;
                max-width: 500px;
            }

            .form-panel {
                height: auto;
                justify-content: center;
                padding: 40px 30px 50px;
            }

            .form { max-width: 440px; }
        }

        /* Handphone */
        @media (max-width: 500px) {
            .art-panel { padding: 15px; }
            .form-panel { padding: 35px 22px 40px; }
            .form { max-width: 100%; }
            h1 { font-size: 30px; }
            .subtitle { font-size: 14px; margin-bottom: 26px; }
            input,
            .submit { height: 44px; }
        }

    </style>
</head>

<body>

    <main class="page">

        <!-- Gambar FeedbackRise -->
        <section class="art-panel" aria-label="FeedbackRise">
            <img
                class="brand-art"
                src="{{ asset('images/feedbackrise-panel.png') }}"
                alt="Speed up your work with our Web App, dengan maskot FeedbackRise"
            >
        </section>

        <!-- Form Register -->
        <section class="form-panel">

            <div class="form">

                <h1>Create an account</h1>

                <p class="subtitle">
                    Create your account to access
                    your analytics dashboard.
                </p>

                <!-- id="register-form" WAJIB, dipakai register.js -->
                <form id="register-form" novalidate>

                    <div class="field">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="Enter Email"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="username">Username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            placeholder="Enter Username"
                            autocomplete="username"
                            value="{{ old('username') }}"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter Password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            placeholder="Enter Password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <!-- Elemen pesan WAJIB, dipakai register.js -->
                    <p id="register-error" class="error" role="alert" hidden></p>
                    <p id="register-success" class="success" role="status" hidden></p>

                    <!-- id="register-button" WAJIB, dipakai register.js -->
                    <button type="submit" class="submit" id="register-button">
                        Sign Up
                    </button>
                </form>

                <p class="signup">
                    Already have an account?
                    <a href="{{ url('/login') }}">Login Here</a>
                </p>

            </div>

        </section>

    </main>

    <!-- WAJIB: memuat JS hasil compile Mix (Firebase) -->
    <script src="{{ mix('js/app.js') }}"></script>

</body>
</html>