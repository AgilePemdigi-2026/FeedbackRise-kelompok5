<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password | FeedbackRise</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body { width: 100%; height: 100%; margin: 0; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f6f6f6; color: #111; font-family: 'Nunito Sans', Arial, sans-serif; }
        .page { width: 100%; height: 100vh; display: grid; grid-template-columns: 54% 46%; background: #fff; overflow: hidden; }
        .art-panel { grid-column: 2; grid-row: 1; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 24px; overflow: hidden; }
        .brand-art { display: block; width: auto; height: calc(100vh - 48px); max-width: 100%; object-fit: contain; }
        .form-panel { grid-column: 1; grid-row: 1; width: 100%; height: 100%; display: flex; align-items: center; justify-content: flex-end; padding: 40px 8% 40px 5%; }
        .form { width: 100%; max-width: 390px; }
        h1 { margin: 0 0 7px; font-size: clamp(25px, 2vw, 32px); line-height: 1.2; font-weight: 800; letter-spacing: -.5px; }
        .subtitle { margin: 0 0 32px; color: #555; font-size: clamp(11px, .85vw, 13px); line-height: 1.5; }
        .field { margin: 0 0 20px; }
        label { display: block; margin-bottom: 7px; font-size: clamp(11px, .8vw, 13px); line-height: 1.2; font-weight: 800; }
        input { width: 100%; height: 42px; padding: 0 15px; border: 0; border-radius: 21px; outline: none; background: #e5e5e5; color: #222; font: 600 12px 'Nunito Sans', Arial, sans-serif; }
        input::placeholder { color: #858585; }
        input:focus { box-shadow: 0 0 0 2px #2854c5; }
        .hint { margin: -8px 0 17px; color: #777; font-size: 11px; line-height: 1.5; }
        .submit { width: 100%; height: 42px; margin-top: 5px; border: 0; border-radius: 21px; background: #4054c6; color: #fff; font: 800 12px 'Nunito Sans', Arial, sans-serif; cursor: pointer; }
        .submit:hover { background: #3347b5; }
        .success { display: none; margin-top: 13px; padding: 10px 12px; border-radius: 9px; background: #edf5ed; color: #276136; font-size: 11px; line-height: 1.45; }
        .success.show { display: block; }
        .back { margin: 20px 0 0; text-align: center; color: #666; font-size: 10px; }
        .back a { color: #2854c5; font-weight: 800; text-decoration: none; }
        .back a:hover { text-decoration: underline; }
        @media (max-width: 1200px) {
            .page { grid-template-columns: 54% 46%; }
            .art-panel { padding: 20px; }
            .form-panel { padding-left: 4%; padding-right: 8%; }
            .form { max-width: 350px; }
        }
        @media (max-height: 750px) and (min-width: 701px) {
            .art-panel { padding-top: 18px; padding-bottom: 18px; }
            .brand-art { height: calc(100vh - 36px); }
            .form-panel { padding-top: 25px; padding-bottom: 25px; }
            h1 { font-size: 27px; }
            .subtitle { margin-bottom: 24px; }
            .field { margin-bottom: 15px; }
            input, .submit { height: 38px; }
        }
        @media (max-width: 700px) {
            .page { height: auto; min-height: 100vh; grid-template-columns: 1fr; }
            .form-panel { grid-column: 1; grid-row: 1; height: auto; justify-content: center; padding: 40px 30px 35px; }
            .form { max-width: 400px; }
            .art-panel { grid-column: 1; grid-row: 2; height: auto; padding: 20px; }
            .brand-art { width: 100%; height: auto; max-width: 500px; }
        }
        @media (max-width: 500px) {
            .art-panel { padding: 15px; }
            .form-panel { padding: 35px 22px 30px; }
            .form { max-width: 100%; }
            h1 { font-size: 24px; }
            .subtitle { font-size: 11px; margin-bottom: 25px; }
            input, .submit { height: 38px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="art-panel" aria-label="FeedbackRise">
            <img class="brand-art" src="{{ asset('images/feedbackrise-panel.png') }}" alt="Speed up your work with our Web App, dengan maskot FeedbackRise">
        </section>
        <section class="form-panel">
            <div class="form">
                <h1>Lupa password?</h1>
                <p class="subtitle">Masukkan email yang terdaftar. Kami akan mengirim instruksi untuk mengatur ulang password Anda.</p>
                <form id="forgotForm">
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="Enter Email" autocomplete="email" required>
                    </div>
                    <p class="hint">Pastikan alamat email yang dimasukkan benar.</p>
                    <button class="submit" type="submit">Send Email</button>
                    <div id="success" class="success" role="status" aria-live="polite">Ini masih demo frontend. Pengiriman email dan tautan reset perlu dihubungkan ke backend.</div>
                </form>
                <p class="back">Remember your password? <a href="{{ url('/login') }}">Sign In</a></p>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', function (event) {
            event.preventDefault();
            if (!this.reportValidity()) return;
            document.getElementById('success').classList.add('show');
        });
    </script>
</body>
</html>






