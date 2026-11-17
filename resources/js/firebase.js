// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getAuth } from "firebase/auth";
import { getFirestore } from "firebase/firestore";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyD2yX6y5iBDeuYyW6gcGZT_mnz1JUAuE90",
  authDomain: "feedbackrise-db.firebaseapp.com",
  projectId: "feedbackrise-db",
  storageBucket: "feedbackrise-db.firebasestorage.app",
  messagingSenderId: "613138262691",
  appId: "1:613138262691:web:0b9763932418a14743ed16",
  measurementId: "G-RKSBC78F0N"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
export const auth = getAuth(app);
export const db = getFirestore(app);

export default app;


