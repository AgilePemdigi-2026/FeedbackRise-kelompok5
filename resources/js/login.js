import {
    signInWithEmailAndPassword
} from "firebase/auth";

import { auth } from "./firebase";

const loginForm = document.getElementById("login-form");

if (loginForm) {
    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const email = loginForm.email.value.trim();
        const password = loginForm.password.value;
        const errorElement = document.getElementById("login-error");
        const button = document.getElementById("login-button");

        errorElement.hidden = true;
        button.disabled = true;
        button.textContent = "Signing in...";

        try {
            // Simulasi system error untuk Scenario 6
            if (window.__simulateLoginSystemError) {
                throw new Error("SIMULATED_SYSTEM_ERROR");
            }

            // Langsung login menggunakan Firebase Authentication
            await signInWithEmailAndPassword(auth, email, password);

            // Login berhasil
            window.location.href = "/home";

        } catch (error) {
            console.error("Login error:", error.code, error.message);

            // System error
            if (error.message === "SIMULATED_SYSTEM_ERROR") {
                errorElement.textContent =
                    "Login gagal. Periksa koneksi dan data akun kamu.";

                errorElement.hidden = false;
                return;
            }

            // Akun tidak terdaftar: arahkan ke halaman registrasi
            if (error.code === "auth/user-not-found") {
                errorElement.textContent =
                    "Akun belum terdaftar. Mengalihkan ke halaman registrasi...";

                errorElement.hidden = false;

                setTimeout(() => {
                    window.location.href = "/register";
                }, 1500);

                return;
            }

            // Password salah / credential tidak valid
            errorElement.textContent =
                "Email atau password tidak sesuai.";

            errorElement.hidden = false;

        } finally {
            button.disabled = false;
            button.textContent = "Sign In";
        }
    });
}