import json
import unittest
from types import SimpleNamespace
from unittest.mock import patch

from fastapi import HTTPException
from pydantic import ValidationError

import main


class AnalysisTests(unittest.TestCase):
    def test_empty_input_is_rejected(self):
        with self.assertRaises(ValidationError):
            main.IssueRequest(issues=[])

    def test_one_issue_produces_one_group(self):
        groups, info = main.group_issues(main.prepare_issues(["Export PDF reports"]))
        self.assertEqual(len(groups), 1)
        self.assertEqual(info["best_k"], 1)
        self.assertIsNone(info["best_silhouette_score"])

    def test_identical_texts_produce_one_group_without_forcing_empty_clusters(self):
        groups, info = main.group_issues(main.prepare_issues(["Add dark mode"] * 4))
        self.assertEqual(len(groups), 1)
        self.assertEqual(groups[0]["issue_count"], 4)
        self.assertEqual(info["silhouette_results"], [])

    def test_two_different_issues_do_not_attempt_invalid_silhouette(self):
        groups, info = main.group_issues(main.prepare_issues(["Dark theme mode", "Export PDF report"]))
        self.assertEqual(len(groups), 2)
        self.assertIsNone(info["best_silhouette_score"])

    def test_punctuation_only_text_is_rejected(self):
        with self.assertRaises(HTTPException) as error:
            main.group_issues(main.prepare_issues(["!!!", "..."]))
        self.assertEqual(error.exception.status_code, 422)

    def test_counts_ranking_and_evidence_use_actual_tfidf_and_kmeans(self):
        request = main.IssueRequest(issues=[
            main.Issue(number=11, title="Add dark mode"),
            main.Issue(number=12, title="Add dark mode"),
            main.Issue(number=13, title="Add dark mode"),
            main.Issue(number=14, title="Export PDF reports"),
        ])
        class FakeClient:
            def __enter__(self):
                return self
            def __exit__(self, *args):
                pass
        def summary(_client, groups):
            return main.AnalysisOutput(overall_summary="Ringkasan", themes=[
                main.ThemeAnalysis(cluster=g["cluster"], category="Fitur", summary="Kebutuhan", recommendation="Kembangkan fitur")
                for g in reversed(groups)
            ]), {}
        with patch.object(main, "get_client", return_value=FakeClient()), \
             patch.object(main, "summarize_groups", side_effect=summary):
            result = main.analyze(request)
        self.assertEqual(result["method"], "TF-IDF + K-Means")
        self.assertEqual(result["recommendations"][0]["issue_count"], 3)
        self.assertEqual(result["recommendations"][0]["percentage"], 75.0)
        self.assertEqual(result["recommendations"][0]["issue_numbers"], [11, 12, 13])
        self.assertEqual(result["clustering"]["best_k"], 2)
        self.assertEqual(result["clustering"]["tfidf_shape"][0], 4)
        self.assertEqual(result["usage"]["generation_requests"], 1)

    def test_both_languages_are_vectorized_without_claiming_translation(self):
        groups, info = main.group_issues(main.prepare_issues(["Dark mode theme", "Mode gelap tema"]))
        self.assertEqual(sum(group["issue_count"] for group in groups), 2)
        self.assertGreater(info["total_features"], 0)

    def test_invented_or_missing_cluster_is_rejected(self):
        output = {"overall_summary": "Example", "themes": [{
            "cluster": 99, "category": "X", "summary": "Y", "recommendation": "Z"
        }]}
        client = SimpleNamespace(models=SimpleNamespace(generate_content=lambda **kwargs: SimpleNamespace(text=json.dumps(output))))
        with self.assertRaises(HTTPException) as error:
            main.summarize_groups(client, [{"cluster": 0}])
        self.assertEqual(error.exception.status_code, 502)

    def test_provider_quota_is_reported_as_429(self):
        with self.assertRaises(HTTPException) as error:
            main.provider_error(SimpleNamespace(code=429), "ringkasan/rekomendasi")
        self.assertEqual(error.exception.status_code, 429)


if __name__ == "__main__":
    unittest.main()
