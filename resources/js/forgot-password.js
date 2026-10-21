
import { sendPasswordResetEmail } from "firebase/auth";
import { auth } from "./firebase";

const form = document.getElementById("forgotForm");

if (form) {
    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!form.reportValidity()) return;

        const email = document.getElementById("email").value.trim();
        const success = document.getElementById("success");
        const button = form.querySelector('button[type="submit"]');

        success.classList.remove("show");
        button.disabled = true;
        button.textContent = "Sending...";

        try {
            await sendPasswordResetEmail(auth, email);

            success.textContent =
                "Jika email terdaftar, instruksi reset password akan dikirim. Periksa inbox atau folder spam.";
            success.classList.add("show");
            form.reset();
        } catch (error) {
            console.error("Reset password error:", error.code, error.message);

            success.textContent =
                error.code === "auth/invalid-email"
                    ? "Format email tidak valid."
                    : "Gagal mengirim email reset password. Periksa konfigurasi Firebase dan coba lagi.";

            success.classList.add("show");
        } finally {
            button.disabled = false;
            button.textContent = "Send Email";
        }
    });
}
