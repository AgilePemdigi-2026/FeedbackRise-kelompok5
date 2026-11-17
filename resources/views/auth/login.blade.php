<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | FeedbackRise</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

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

            padding: 40px 8% 40px 5%;
        }

        .form {
            width: 100%;
            max-width: 390px;
        }


        /* Judul */

        h1 {
            margin: 0 0 7px;

            font-size: clamp(25px, 2vw, 32px);
            line-height: 1.2;

            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .subtitle {
            margin: 0 0 32px;

            color: #555;

            font-size: clamp(11px, 0.85vw, 13px);
            line-height: 1.5;
        }


        /* Field */

        .field {
            margin: 0 0 20px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 7px;
        }

        label {
            display: block;

            font-size: clamp(11px, 0.8vw, 13px);
            line-height: 1.2;

            font-weight: 800;
        }


        /* Forgot password */

        .forgot-link {
            color: #2854c5;

            font-size: clamp(9px, 0.65vw, 11px);
            font-weight: 800;

            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }


        /* Input */

        input {
            width: 100%;
            height: 42px;

            padding: 0 15px;

            border: 0;
            border-radius: 21px;

            outline: none;

            background: #e5e5e5;
            color: #222;

            font: 600 12px 'Nunito Sans', Arial, sans-serif;
        }

        input::placeholder {
            color: #858585;
        }

        input:focus {
            box-shadow: 0 0 0 2px #2854c5;
        }


        /* Tombol Sign In */

        .submit {
            width: 100%;
            height: 42px;

            margin-top: 5px;

            border: 0;
            border-radius: 21px;

            background: #4054c6;
            color: #fff;

            font: 800 12px 'Nunito Sans', Arial, sans-serif;

            cursor: pointer;
        }

        .submit:hover {
            background: #3347b5;
        }


        /* Sign Up */

        .signup {
            margin: 20px 0 0;

            text-align: center;

            color: #666;

            font-size: 10px;
        }

        .signup a {
            color: #2854c5;

            font-weight: 800;

            text-decoration: none;
        }

        .signup a:hover {
            text-decoration: underline;
        }


        /* Laptop kecil */

        @media (max-width: 1200px) {

            .page {
                grid-template-columns: 45% 55%;
            }

            .art-panel {
                padding-left: 20px;
            }

            .form-panel {
                padding-left: 4%;
                padding-right: 8%;
            }

            .form {
                max-width: 350px;
            }
        }


        /* Laptop dengan tinggi layar lebih kecil */

        @media (max-height: 750px) and (min-width: 701px) {

            .art-panel {
                padding-top: 18px;
                padding-bottom: 18px;
            }

            .brand-art {
                height: calc(100vh - 36px);
            }

            .form-panel {
                padding-top: 25px;
                padding-bottom: 25px;
            }

            h1 {
                font-size: 27px;
            }

            .subtitle {
                margin-bottom: 24px;
            }

            .field {
                margin-bottom: 15px;
            }

            input {
                height: 38px;
            }

            .submit {
                height: 38px;
            }
        }


        /* Tablet */

        @media (max-width: 700px) {

            body {
                min-height: 100vh;
            }

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

            .form {
                max-width: 400px;
            }
        }


        /* Handphone */

        @media (max-width: 500px) {

            .art-panel {
                padding: 15px;
            }

            .form-panel {
                padding: 35px 22px 40px;
            }

            .form {
                max-width: 100%;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 11px;
                margin-bottom: 25px;
            }

            input,
            .submit {
                height: 38px;
            }
        }
        
    /* Pesan error validasi */
.error-message {
    margin: 6px 0 0;
    color: #d93025;
    font-size: 11px;
}

    </style>
</head>

<body>

    <main class="page">


        <!-- Gambar FeedbackRise -->

        <section
            class="art-panel"
            aria-label="FeedbackRise"
        >

            <img
                class="brand-art"
                src="{{ asset('images/feedbackrise-panel.png') }}"
                alt="Speed up your work with our Web App, dengan maskot FeedbackRise"
            >

        </section>


        <!-- Form Sign In -->

        <section class="form-panel">

            <div class="form">

                <h1>
                    Sign In
                </h1>

                <p class="subtitle">
                    Sign in to access your analytics
                    and manage your work.
                </p>




<form id="login-form">
    <p id="login-error" class="error-message" role="alert" hidden></p>

    <div class="field">
        <label for="email">Email</label>
        <input
            id="email"
            name="email"
            type="email"
            placeholder="Enter Email"
            autocomplete="email"
            required
        >
    </div>

    <div class="field">
        <div class="label-row">
            <label for="password">Password</label>
            <a href="{{ url('/forgot-password') }}" class="forgot-link">
                Forgot Password?
            </a>
        </div>

        <input
            id="password"
            name="password"
            type="password"
            placeholder="Enter Password"
            autocomplete="current-password"
            required
        >
    </div>

    <button class="submit" type="submit" id="login-button">
        Sign In
    </button>
</form>




                <!-- Sign Up -->

                <p class="signup">

                    Don't have an account?

                    <a href="{{ route('register') }}">
                        Sign Up
                    </a>

                </p>

            </div>

        </section>

    </main>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>