
document.addEventListener('DOMContentLoaded', function () {

    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');
    const logoutButton = document.getElementById('logoutButton');

    // PROFILE DROPDOWN
    if (profileButton && profileMenu) {

        profileButton.addEventListener('click', function (event) {

            event.stopPropagation();

            const isOpen = profileMenu.classList.toggle('show');

            profileButton.setAttribute(
                'aria-expanded',
                String(isOpen)
            );

        });

        // Tutup dropdown ketika klik di luar
        document.addEventListener('click', function (event) {

            if (
                !profileButton.contains(event.target) &&
                !profileMenu.contains(event.target)
            ) {

                profileMenu.classList.remove('show');

                profileButton.setAttribute('aria-expanded', 'false');

            }

        });

        // Tutup dropdown dengan tombol Escape
        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {

                profileMenu.classList.remove('show');

                profileButton.setAttribute('aria-expanded', 'false');

            }

        });

    }


    // LOGOUT
    if (logoutButton) {

        logoutButton.addEventListener('click', function () {

            const confirmed = confirm(
                'Apakah kamu yakin ingin logout?'
            );

            if (confirmed) {

                window.location.href = '/login';

            }

        });

    }

});
