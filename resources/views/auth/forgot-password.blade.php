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
        body { min-height: 100vh; margin: 0; padding: 30px 20px; display: grid; place-items: center; background: #f6f6f6; color: #111; font-family: 'Nunito Sans', Arial, sans-serif; }
        .page { width: min(100%, 790px); min-height: 510px; display: grid; grid-template-columns: 1fr 1fr; gap: 0; padding: 14px; border: 1px solid #222; background: #fff; }
        .art-panel { grid-column: 2; grid-row: 1; position: relative; overflow: hidden; border-radius: 10px; background: #ffd65b; }
        .brand-art { display: block; width: 100%; height: auto; }
        .form-panel { grid-column: 1; grid-row: 1; display: flex; align-items: center; justify-content: center; padding: 42px 46px; }
        .form { width: 100%; max-width: 310px; }
        h1 { margin: 0 0 3px; font-size: 25px; line-height: 1.2; font-weight: 800; letter-spacing: -.5px; }
        .subtitle { margin: 0 0 28px; color: #555; font-size: 12px; line-height: 1.45; }
        .field { margin: 0 0 17px; }
        label { display: block; margin-bottom: 5px; font-size: 12px; line-height: 1.2; font-weight: 800; }
        input { width: 100%; height: 35px; padding: 0 13px; border: 0; border-radius: 18px; outline: none; background: #e5e5e5; color: #222; font: 600 12px 'Nunito Sans', Arial, sans-serif; }
        input::placeholder { color: #858585; }
        input:focus { box-shadow: 0 0 0 2px #2854c5; }
        .hint { margin: -8px 0 17px; color: #777; font-size: 10px; line-height: 1.5; }
        .submit { width: 100%; height: 36px; border: 0; border-radius: 20px; background: #2854c5; color: white; font: 800 12px 'Nunito Sans', Arial, sans-serif; cursor: pointer; }
        .submit:hover { background: #1947ba; }
        .success { display: none; margin-top: 13px; padding: 10px 12px; border-radius: 9px; background: #edf5ed; color: #276136; font-size: 11px; line-height: 1.45; }
        .success.show { display: block; }
        .back { margin: 16px 0 0; text-align: center; color: #666; font-size: 10px; }
        .back a { color: #2854c5; font-weight: 800; text-decoration: none; }
        .back a:hover { text-decoration: underline; }
        @media (max-width: 620px) {
            body { padding: 18px 12px; }
            .page { max-width: 420px; grid-template-columns: 1fr; padding: 10px; }
            .art-panel { grid-column: 1; grid-row: 2; }
            .form-panel { grid-column: 1; grid-row: 1; padding: 36px 24px 28px; }
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




