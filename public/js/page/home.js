/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./resources/js/page/home.js ***!
  \***********************************/
document.addEventListener('DOMContentLoaded', function () {
  var profileButton = document.getElementById('profileButton');
  var profileMenu = document.getElementById('profileMenu');
  var logoutButton = document.getElementById('logoutButton');
  var issueForm = document.getElementById('issueForm');
  var issueUrl = document.getElementById('issueUrl');

  /* Profile menu */

  profileButton.addEventListener('click', function (event) {
    event.stopPropagation();
    profileMenu.classList.toggle('show');
  });

  /* Close profile menu */

  document.addEventListener('click', function (event) {
    if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
      profileMenu.classList.remove('show');
    }
  });

  /* Logout */

  logoutButton.addEventListener('click', function () {
    var confirmation = confirm('Apakah kamu yakin ingin logout?');
    if (confirmation) {
      window.location.href = '/login';
    }
  });

  /* Input issue */

  issueForm.addEventListener('submit', function (event) {
    var url = issueUrl.value.trim();
    if (url === '') {
      event.preventDefault();
      alert('Silakan masukkan URL issue terlebih dahulu.');
      issueUrl.focus();
      return;
    }
  });
});
/******/ })()
;