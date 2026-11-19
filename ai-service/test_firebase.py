from firebase_config import db

try:
    docs = db.collection("users").limit(1).get()

    print("✅ Koneksi Firestore berhasil!")
    print(f"Collection users dapat diakses.")

except Exception as e:
    print("❌ Koneksi Firestore gagal!")
    print("Error:", e)