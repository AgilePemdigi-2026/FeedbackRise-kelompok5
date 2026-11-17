
document.addEventListener('DOMContentLoaded', function () {

    const status = document.getElementById('analysisStatus');
    const details = document.getElementById('analysisDetails');
    const empty = document.getElementById('analysisEmpty');

    const savedData = sessionStorage.getItem('feedbackriseAnalysis');

    if (!savedData) {
        status.hidden = false;
        status.textContent =
            'Belum ada hasil analisis. Silakan kembali ke Home dan lakukan analisis terlebih dahulu.';

        status.classList.add('is-error');
        empty.hidden = false;
        return;
    }

    try {
        const data = JSON.parse(savedData);
        const analysis = data.analysis;

        if (!analysis || !Array.isArray(analysis.recommendations)) {
            throw new Error('Data hasil analisis tidak lengkap.');
        }

        const recommendations = analysis.recommendations.slice(0, 3);

        // Top Priority Issue
        const topIssue = recommendations[0];

        document.getElementById('topIssueTitle').textContent =
            topIssue?.category || '-';

        document.getElementById('topIssueSummary').textContent =
            topIssue?.summary || '-';

        document.getElementById('totalFeedback').textContent =
            (data.total_issues ?? 0) + ' Feedback';

        // Top 3 Recommendations
        const topContainer = document.getElementById('topRecommendations');
        topContainer.replaceChildren();

        recommendations.forEach(function (item, index) {

            const card = document.createElement('article');
            card.className = 'top-recommendation-card';

            const badge = document.createElement('span');
            badge.className = 'priority-badge';
            badge.textContent = 'Priority ' + (item.rank ?? index + 1);
            card.appendChild(badge);

            const title = document.createElement('h3');
            title.textContent = item.category || 'Issue category';
            card.appendChild(title);

            const count = document.createElement('p');
            count.textContent =
                (item.issue_count ?? 0) + ' Feedback';
            card.appendChild(count);

            const summary = document.createElement('p');
            summary.textContent = item.summary || '-';
            card.appendChild(summary);

            const label = document.createElement('p');
            label.className = 'recommendation-label';
            label.textContent = 'Recommendations';
            card.appendChild(label);

            const recommendation = document.createElement('p');
            recommendation.textContent = item.recommendation || '-';
            card.appendChild(recommendation);

            topContainer.appendChild(card);
        });

        // Detail Table
        const tableBody = document.getElementById('detailTableBody');
        tableBody.replaceChildren();

        recommendations.forEach(function (item) {

            const row = document.createElement('tr');

            const issueCell = document.createElement('td');
            issueCell.textContent = item.category || '-';

            const summaryCell = document.createElement('td');
            summaryCell.textContent = item.summary || '-';

            const recommendationCell = document.createElement('td');
            recommendationCell.textContent = item.recommendation || '-';

            row.appendChild(issueCell);
            row.appendChild(summaryCell);
            row.appendChild(recommendationCell);

            tableBody.appendChild(row);
        });

        // Tampilkan hasil
        details.hidden = false;
        empty.hidden = true;
        status.hidden = true;

    } catch (error) {

        console.error('Analysis display error:', error);

        status.hidden = false;
        status.classList.add('is-error');
        status.textContent =
            error.message || 'Terjadi kesalahan saat menampilkan hasil analisis.';
    }

});
