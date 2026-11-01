import json

from fastapi import FastAPI
from pydantic import BaseModel
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from collections import Counter

import os
from dotenv import load_dotenv
from google import genai

load_dotenv()

client = genai.Client(
    api_key=os.getenv("GEMINI_API_KEY")
)

app = FastAPI()


class IssueRequest(BaseModel):
    issues: list[str]


def analyze_with_gemini(issues):
    prompt = f"""
Analisis kumpulan GitHub Issue berikut.

Issues:
{chr(10).join(f"- {issue}" for issue in issues)}

Kembalikan hasil dalam format JSON dengan struktur berikut:

{{
    "category": "kategori utama issue",
    "summary": "ringkasan masalah",
    "recommendation": "rekomendasi solusi"
}}

Ketentuan:
- category harus menggambarkan kategori utama dari masalah.
- summary harus merangkum masalah berdasarkan Issue yang diberikan.
- recommendation harus memberikan saran solusi yang relevan.
- Gunakan bahasa Indonesia.
- Singkat, jelas, dan sesuai dengan isi Issue.
- Jangan membuat informasi yang tidak terdapat atau tidak dapat disimpulkan dari Issue.
- Hanya kembalikan JSON, tanpa markdown atau penjelasan tambahan.
"""

    response = client.models.generate_content(
        model="gemini-3.5-flash-lite",
        contents=prompt
    )

    return json.loads(response.text)


@app.get("/")
def root():
    return {
        "message": "FeedbackRise AI Service is running"
    }


@app.post("/analyze")
def analyze(request: IssueRequest):
    vectorizer = TfidfVectorizer()

    tfidf_matrix = vectorizer.fit_transform(request.issues)

    # Jika jumlah Issue kurang dari 2,
    # K-Means dan silhouette score tidak dapat dilakukan.
    if len(request.issues) < 2:
        gemini_analysis = analyze_with_gemini(request.issues)

        return {
            "message": "Analisis Issue berhasil dilakukan",
            "total_issues": len(request.issues),
            "total_features": len(
                vectorizer.get_feature_names_out()
            ),
            "tfidf_shape": list(tfidf_matrix.shape),
            "silhouette_results": [],
            "best_k": 1,
            "best_silhouette_score": None,
            "best_cluster_labels": [0] * len(request.issues),
            "top_3_issues": [
                {
                    "cluster": 0,
                    "issue_count": len(request.issues),
                    "issues": request.issues,
                    "ai_analysis": gemini_analysis
                }
            ]
        }

    results = []

    # Menguji jumlah cluster
    for k in range(
        2,
        min(4, len(request.issues) - 1) + 1
    ):
        kmeans = KMeans(
            n_clusters=k,
            random_state=42,
            n_init=10
        )

        cluster_labels = kmeans.fit_predict(
            tfidf_matrix
        )

        score = silhouette_score(
            tfidf_matrix,
            cluster_labels
        )

        results.append({
            "k": k,
            "silhouette_score": round(
                float(score),
                4
            ),
            "cluster_labels": cluster_labels.tolist()
        })

    # Memilih jumlah cluster terbaik
    best_result = max(
        results,
        key=lambda result: result["silhouette_score"]
    )

    best_labels = best_result["cluster_labels"]

    # Menghitung jumlah Issue pada setiap cluster
    cluster_counts = Counter(best_labels)

    # Mengurutkan cluster berdasarkan jumlah Issue terbanyak
    ranked_clusters = sorted(
        cluster_counts.items(),
        key=lambda item: item[1],
        reverse=True
    )

    # Mengambil maksimal 3 cluster teratas
    top_clusters = ranked_clusters[:3]

    top_issues = []

    for cluster_id, issue_count in top_clusters:
        cluster_issues = [
            issue
            for issue, label in zip(
                request.issues,
                best_labels
            )
            if label == cluster_id
        ]

        gemini_analysis = analyze_with_gemini(
            cluster_issues
        )

        top_issues.append({
            "cluster": cluster_id,
            "issue_count": issue_count,
            "issues": cluster_issues,
            "ai_analysis": gemini_analysis
        })

    return {
        "message": "Analisis Issue berhasil dilakukan",
        "total_issues": len(request.issues),
        "total_features": len(
            vectorizer.get_feature_names_out()
        ),
        "tfidf_shape": list(
            tfidf_matrix.shape
        ),
        "silhouette_results": results,
        "best_k": best_result["k"],
        "best_silhouette_score": best_result[
            "silhouette_score"
        ],
        "best_cluster_labels": best_labels,
        "top_3_issues": top_issues
    }