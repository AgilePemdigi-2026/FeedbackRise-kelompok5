document.addEventListener('DOMContentLoaded', function () {

    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');

    const logoutButton = document.getElementById('logoutButton');

    const issueForm = document.getElementById('issueForm');
    const issueUrl = document.getElementById('issueUrl');


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


    /* Input issue */

    issueForm.addEventListener('submit', function (event) {

        const url = issueUrl.value.trim();

        if (url === '') {

            event.preventDefault();

            alert('Silakan masukkan URL issue terlebih dahulu.');

            issueUrl.focus();

            return;
        }

    });

});