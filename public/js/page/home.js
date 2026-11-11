document.addEventListener('DOMContentLoaded', function () {
    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');
    const logoutButton = document.getElementById('logoutButton');
    const issueForm = document.getElementById('issueForm');
    const issueUrl = document.getElementById('issueUrl');
    const issueLimit = document.getElementById('issueLimit');
    const issueState = document.getElementById('issueState');
    const submitButton = issueForm.querySelector('.submit-button');
    const status = document.getElementById('analysisStatus');
    const results = document.getElementById('analysisResults');

    profileButton.addEventListener('click', function (event) {
        event.stopPropagation();
        profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', function (event) {
        if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) profileMenu.classList.remove('show');
    });
    logoutButton.addEventListener('click', function () {
        if (confirm('Apakah kamu yakin ingin logout?')) window.location.href = '/login';
    });

    function addText(parent, tag, text, className) {
        const element = document.createElement(tag);
        element.textContent = text;
        if (className) element.className = className;
        parent.appendChild(element);
        return element;
    }

    function showResults(data) {
        const analysis = data.analysis;
        document.getElementById('analysisMeta').textContent = data.repository + ' · ' +
            data.total_issues + ' issue dianalisis · batas ' + data.requested_limit +
            ' · status ' + data.state + ' · ' + data.github_pages + ' halaman GitHub';
        document.getElementById('analysisSummary').textContent = analysis.overall_summary;
        document.getElementById('analysisNote').textContent = data.selection + ' ' + analysis.note +
            (data.page_scan_limit_reached ? ' Pengambilan berhenti pada batas 20 halaman GitHub.' : '');
        const list = document.getElementById('recommendationList');
        list.replaceChildren();
        analysis.recommendations.forEach(function (item) {
            const card = document.createElement('article');
            card.className = 'recommendation-card';
            addText(card, 'p', 'Prioritas ' + item.rank + ' · ' + item.issue_count + ' issue (' + item.percentage + '%)', 'recommendation-count');
            addText(card, 'h3', item.category);
            addText(card, 'p', item.summary);
            addText(card, 'h4', 'Rekomendasi pengembangan');
            addText(card, 'p', item.recommendation);
            const evidence = addText(card, 'div', '', 'recommendation-evidence');
            addText(evidence, 'span', 'Bukti issue: ');
            item.issue_numbers.forEach(function (number) {
                const link = addText(evidence, 'a', '#' + number);
                link.href = 'https://github.com/' + data.repository + '/issues/' + number;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
            });
            list.appendChild(card);
        });
        results.hidden = false;
    }

    issueForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (submitButton.disabled) return;
        const url = issueUrl.value.trim();
        if (!/^https:\/\/github\.com\/[A-Za-z0-9-]+\/[A-Za-z0-9_.-]+\/issues\/?$/i.test(url)) {
            status.textContent = 'Masukkan URL https://github.com/owner/repository/issues.';
            status.className = 'analysis-status is-error';
            return;
        }
        if (!issueLimit.reportValidity()) return;
        submitButton.disabled = true;
        issueForm.setAttribute('aria-busy', 'true');
        results.hidden = true;
        status.className = 'analysis-status';
        status.textContent = 'Mengambil issue dan menyiapkan rekomendasi. Mohon tunggu...';
        try {
            const response = await fetch('/api/github/issues', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify({issue_url: url, max_issues: Number(issueLimit.value), state: issueState.value})
            });
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (error) {
                throw new Error('Server mengembalikan respons bukan JSON. Periksa terminal Laravel dan storage/logs/laravel.log.');
            }
            if (!response.ok) throw new Error(data.message || data.detail || 'Analisis gagal.');
            showResults(data);
            console.log('Hasil analisis:', data);
            status.textContent = 'Selesai. Rekomendasi ditampilkan di bawah.';
            results.scrollIntoView({behavior: 'smooth', block: 'start'});
        } catch (error) {
            status.className = 'analysis-status is-error';
            status.textContent = error.message || 'Analisis gagal. Periksa terminal Laravel dan Uvicorn.';
        } finally {
            submitButton.disabled = false;
            issueForm.removeAttribute('aria-busy');
        }
    });
});
