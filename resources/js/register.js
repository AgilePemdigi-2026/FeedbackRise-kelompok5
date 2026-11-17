
import {
    createUserWithEmailAndPassword,
    updateProfile
} from "firebase/auth";

import {
    doc,
    setDoc,
    serverTimestamp
} from "firebase/firestore";

import { auth, db } from "./firebase";

const registerForm = document.getElementById("register-form");

if (registerForm) {
    registerForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const email = registerForm.querySelector("#email").value.trim();
        const username = registerForm.querySelector("#username").value.trim();
        const password = registerForm.querySelector("#password").value;
        const confirmation = registerForm.querySelector(
            "#password_confirmation"
        ).value;

        const errorElement = document.getElementById("register-error");
        const successElement = document.getElementById("register-success");
        const button = document.getElementById("register-button");

        errorElement.hidden = true;
        successElement.hidden = true;

        if (username.length === 0) {
            errorElement.textContent = "Username wajib diisi.";
            errorElement.hidden = false;
            return;
        }

        if (password.length < 8) {
            errorElement.textContent =
                "Password harus terdiri dari minimal 8 karakter.";
            errorElement.hidden = false;
            return;
        }

        if (password !== confirmation) {
            errorElement.textContent = "Konfirmasi password tidak cocok.";
            errorElement.hidden = false;
            return;
        }

        button.disabled = true;
        button.textContent = "Creating account...";

        let credential = null;

        try {
            credential = await createUserWithEmailAndPassword(
                auth,
                email,
                password
            );

            await updateProfile(credential.user, {
                displayName: username
            });

            await setDoc(doc(db, "users", credential.user.uid), {
                uid: credential.user.uid,
                username: username,
                email: credential.user.email,
                createdAt: serverTimestamp()
            });

            successElement.textContent =
                "Akun berhasil dibuat. Mengalihkan ke halaman login...";
            successElement.hidden = false;

            await auth.signOut();

            window.location.href = "/login";
        } catch (error) {
            console.error("Registration error:", error);

            if (credential && !error.code) {
                errorElement.textContent =
                    "Akun autentikasi berhasil dibuat, tetapi profil belum berhasil disimpan. Coba login kembali atau periksa Firestore.";
            } else {
                switch (error.code) {
                    case "auth/email-already-in-use":
                        errorElement.textContent =
                            "Email sudah terdaftar.";
                        break;
                    case "auth/invalid-email":
                        errorElement.textContent =
                            "Format email tidak valid.";
                        break;
                    case "auth/weak-password":
                        errorElement.textContent =
                            "Password terlalu lemah.";
                        break;
                    default:
    errorElement.textContent =
        `Error: ${error.code || "unknown"} - ${error.message}`;
    break;
                }
            }

            errorElement.hidden = false;
        } finally {
            button.disabled = false;
            button.textContent = "Sign Up";
        }
    });
}
