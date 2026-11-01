document.addEventListener('DOMContentLoaded', function () {

    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');

    const logoutButton = document.getElementById('logoutButton');

    const issueForm = document.getElementById('issueForm');
    const issueUrl = document.getElementById('issueUrl');

    const submitButton = issueForm.querySelector('.submit-button');


    /* Profile menu */

    profileButton.addEventListener('click', function (event) {

        event.stopPropagation();

        profileMenu.classList.toggle('show');

    });


    /* Close profile menu */

    document.addEventListener('click', function (event) {

        if (
            !profileButton.contains(event.target) &&
            !profileMenu.contains(event.target)
        ) {
            profileMenu.classList.remove('show');
        }

    });


    /* Logout */

    logoutButton.addEventListener('click', function () {

        const confirmation = confirm(
            'Apakah kamu yakin ingin logout?'
        );

        if (confirmation) {
            window.location.href = '/login';
        }

    });


    /* Analyze GitHub Issues */

    issueForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        const url = issueUrl.value.trim();


        /* Validasi input kosong */

        if (url === '') {

            alert(
                'Silakan masukkan URL issue terlebih dahulu.'
            );

            issueUrl.focus();

            return;
        }


        /* Validasi URL GitHub Issues */

        if (
            !url.includes('github.com/') ||
            !url.includes('/issues')
        ) {

            alert(
                'Silakan masukkan URL GitHub Issues yang valid.'
            );

            issueUrl.focus();

            return;
        }


        /* Disable tombol selama proses */

        submitButton.disabled = true;


        try {

            /* Kirim URL ke Laravel */

            const response = await fetch(
                '/api/github/issues',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },

                    body: JSON.stringify({
                        issue_url: url
                    })
                }
            );


            /* Ambil response dari Laravel */

            const data = await response.json();


            /* Cek response */

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Gagal melakukan analisis.'
                );

            }


            /* Tampilkan hasil sementara di Console */

            console.log(
                'Hasil analisis:',
                JSON.stringify(data, null, 2)
            );


            alert(
                'Analisis berhasil dilakukan.'
            );


        } catch (error) {

            console.error(
                'Error:',
                error
            );


            alert(
                error.message ||
                'Terjadi kesalahan saat melakukan analisis.'
            );


        } finally {

            submitButton.disabled = false;

        }

    });

});