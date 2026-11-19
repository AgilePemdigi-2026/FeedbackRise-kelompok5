import firebase_admin
from firebase_admin import credentials, firestore
from pathlib import Path

# Lokasi service account key
BASE_DIR = Path(__file__).resolve().parent
SERVICE_ACCOUNT_PATH = BASE_DIR / "serviceAccountKey.json"

# Inisialisasi Firebase
if not firebase_admin._apps:
    cred = credentials.Certificate(str(SERVICE_ACCOUNT_PATH))
    firebase_admin.initialize_app(cred)

# Koneksi Firestore
db = firestore.client()