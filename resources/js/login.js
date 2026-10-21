import { signInWithEmailAndPassword } from "firebase/auth";
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
            await signInWithEmailAndPassword(auth, email, password);

            window.location.href = "/";
        } catch (error) {
            errorElement.textContent =
                error.code === "auth/invalid-credential"
                    ? "Email atau password tidak sesuai."
                    : "Login gagal. Periksa koneksi dan data akun kamu.";

            errorElement.hidden = false;
        } finally {
            button.disabled = false;
            button.textContent = "Sign In";
        }
    });
}
