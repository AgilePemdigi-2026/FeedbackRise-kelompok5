
document.addEventListener('DOMContentLoaded', function () {

    // Ambil elemen form
    const issueForm = document.getElementById('issueForm');
    const issueUrl = document.getElementById('issueUrl');
    const issueLimit = document.getElementById('issueLimit');
    const issueState = document.getElementById('issueState');


    // Pastikan form dan elemen input tersedia
    if (!issueForm || !issueUrl || !issueLimit || !issueState) {

        console.error('Elemen form analisis tidak ditemukan.');

        return;

    }


    // Ambil tombol Analyze dan status
    const submitButton = issueForm.querySelector('.submit-button');
    const status = document.getElementById('analysisStatus');


    if (!submitButton || !status) {

        console.error('Tombol Analyze atau analysisStatus tidak ditemukan.');

        return;

    }


    // Submit form analisis
    issueForm.addEventListener('submit', async function (event) {

        event.preventDefault();


        // Mencegah pengiriman berulang
        if (submitButton.disabled) return;


        const url = issueUrl.value.trim();


        // Validasi URL GitHub
        const githubPattern =
            /^https:\/\/github\.com\/[A-Za-z0-9-]+\/[A-Za-z0-9_.-]+\/issues\/?$/i;


        if (!githubPattern.test(url)) {

            status.textContent =
                'Masukkan URL dengan format https://github.com/owner/repository/issues';

            status.className = 'analysis-status is-error';

            return;

        }


        // Validasi jumlah issue
        if (!issueLimit.reportValidity()) return;


        // Tampilkan loading
        submitButton.disabled = true;

        issueForm.setAttribute('aria-busy', 'true');

        status.className = 'analysis-status';

        status.textContent =
            'Mengambil issue dan menyiapkan rekomendasi. Mohon tunggu...';


        try {

            // Mengirim permintaan analisis ke Laravel API
            const response = await fetch('/api/github/issues', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'Accept': 'application/json'

                },

                body: JSON.stringify({

                    issue_url: url,

                    max_issues: Number(issueLimit.value),

                    state: issueState.value

                })

            });


            // Membaca respons server
            const responseText = await response.text();

            let data;


            try {

                data = JSON.parse(responseText);

            } catch (error) {

                throw new Error(
                    'Server mengembalikan respons bukan JSON. Periksa Laravel.'
                );

            }


            // Memeriksa keberhasilan respons
            if (!response.ok) {

                throw new Error(

                    data.message ||

                    data.detail ||

                    'Analisis gagal. Silakan coba kembali.'

                );

            }


            // Simpan hasil analisis ke sessionStorage
            sessionStorage.setItem(

                'feedbackriseAnalysis',

                JSON.stringify(data)

            );


            console.log(
                'Hasil analisis berhasil disimpan:',
                data
            );


            // Pindah ke halaman Analysis
            window.location.assign('/analysis');


        } catch (error) {

            // Menampilkan error pada console
            console.error('Analysis error:', error);


            // Menampilkan pesan error pada halaman
            status.className = 'analysis-status is-error';

            status.textContent =
                error.message || 'Analisis gagal. Silakan coba kembali.';


            // Aktifkan kembali tombol Analyze
            submitButton.disabled = false;

            issueForm.removeAttribute('aria-busy');

        }

    });

});
