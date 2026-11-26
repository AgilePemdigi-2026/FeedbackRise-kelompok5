import { auth } from '../firebase';
import { onAuthStateChanged } from 'firebase/auth';

document.addEventListener('DOMContentLoaded', function () {

    const ANALYSIS_KEY = 'feedbackriseAnalysis';

    const list = document.getElementById('historyList');
    const countBadge = document.getElementById('historyCount');
    const searchInput = document.getElementById('historySearch');
    const emptyState = document.getElementById('historyEmpty');
    const noResult = document.getElementById('historyNoResult');

    if (!list || !countBadge || !searchInput || !emptyState || !noResult) {
        console.error('Elemen halaman histori tidak ditemukan.');
        return;
    }

    const TRASH_ICON =
        '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' +
        '<path d="M9 3h6l1 1.5h4V7H4V4.5h4L9 3z"/>' +
        '<path d="M5.5 8.5h13l-1 12a1.5 1.5 0 0 1-1.5 1.4H8a1.5 1.5 0 0 1-1.5-1.4l-1-12z"/>' +
        '</svg>';

    let items = [];
    let currentUser = null;
    let isLoading = true;

    // =====================================
    // FIREBASE AUTHENTICATION
    // =====================================

    function waitForUser() {
        return new Promise(function (resolve, reject) {
            let unsubscribe = () => {};

            unsubscribe = onAuthStateChanged(
                auth,
                function (user) {
                    unsubscribe();

                    if (!user) {
                        reject(new Error(
                            'Session pengguna tidak ditemukan. Silakan login kembali.'
                        ));
                        return;
                    }

                    resolve(user);
                },
                function (error) {
                    unsubscribe();
                    reject(error);
                }
            );
        });
    }

    async function getAuthHeaders() {
        if (!currentUser) {
            throw new Error('Silakan login kembali.');
        }

        const idToken = await currentUser.getIdToken();

        return {
            'Authorization': `Bearer ${idToken}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }

    // =====================================
    // API HISTORY
    // =====================================

    async function loadHistory() {
        try {
            const headers = await getAuthHeaders();

            const response = await fetch('/api/history', {
                method: 'GET',
                headers: headers
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || 'Gagal mengambil histori analisis.'
                );
            }

            items = Array.isArray(data.history)
                ? data.history
                : [];

            items.sort(function (a, b) {
                return new Date(b.createdAt || 0) -
                    new Date(a.createdAt || 0);
            });

        } catch (error) {
            console.error('Gagal mengambil histori:', error);
            showError(error.message || 'Gagal memuat histori.');
        } finally {
            isLoading = false;
            render();
        }
    }

    async function deleteHistoryFromAPI(item) {
        const headers = await getAuthHeaders();

        const response = await fetch(
            `/api/history/${encodeURIComponent(item.id)}`,
            {
                method: 'DELETE',
                headers: headers
            }
        );

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(
                data.message || 'Gagal menghapus histori.'
            );
        }

        return data;
    }

    // =====================================
    // HELPER
    // =====================================

    function formatDate(value) {
        if (!value) return '-';

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) return '-';

        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function createText(tag, className, text) {
        const element = document.createElement(tag);

        if (className) {
            element.className = className;
        }

        element.textContent = text ?? '-';

        return element;
    }

    function getAnalysisData(item) {
        const data = item.data || {};
        const analysis = data.analysis || {};

        return {
            data: data,
            analysis: analysis
        };
    }

    function getPriorityIssue(analysis) {
        const recommendations = Array.isArray(analysis.recommendations)
            ? analysis.recommendations
            : [];

        if (recommendations.length === 0) {
            return null;
        }

        return [...recommendations].sort(function (a, b) {
            return Number(b.issue_count || 0) -
                Number(a.issue_count || 0);
        })[0];
    }

    function showError(message) {
        list.replaceChildren();

        const errorElement = createText(
            'p',
            'history-no-result',
            message
        );

        errorElement.hidden = false;
        list.appendChild(errorElement);
    }

    // =====================================
    // CREATE HISTORY CARD
    // =====================================

    function createCard(item) {
        const result = getAnalysisData(item);
        const data = result.data;
        const analysis = result.analysis;

        const priorityIssue = getPriorityIssue(analysis);

        const card = document.createElement('article');
        card.className = 'history-card';

        const repositoryName =
            data.repository ||
            item.repository ||
            item.title ||
            'Feedback Analysis';

        card.appendChild(
            createText('h2', 'history-card-title', repositoryName)
        );

        card.appendChild(
            createText(
                'p',
                'history-date',
                formatDate(analysis.created_at || item.createdAt)
            )
        );

        card.appendChild(
            createText('p', 'history-label', 'Priority Issue')
        );

        if (priorityIssue) {

            const category =
                priorityIssue.category ||
                priorityIssue.cluster ||
                priorityIssue.title ||
                'Priority Issue';

            card.appendChild(
                createText(
                    'p',
                    'history-priority-category',
                    category
                )
            );

            const issueDetails = [];

            if (priorityIssue.issue_count !== undefined) {
                issueDetails.push(
                    priorityIssue.issue_count + ' related issues'
                );
            }

            if (priorityIssue.percentage !== undefined) {
                issueDetails.push(
                    priorityIssue.percentage + '%'
                );
            }

            if (issueDetails.length > 0) {
                card.appendChild(
                    createText(
                        'p',
                        'history-issue-info',
                        issueDetails.join(' · ')
                    )
                );
            }

        } else {

            card.appendChild(
                createText(
                    'p',
                    'history-priority-category',
                    'Priority issue is not available.'
                )
            );

        }

        card.appendChild(
            createText('p', 'history-label', 'Recommendation')
        );

        card.appendChild(
            createText(
                'p',
                'history-value',
                priorityIssue
                    ? (
                        priorityIssue.recommendation ||
                        priorityIssue.description ||
                        'Recommendation is not available.'
                    )
                    : 'Recommendation is not available.'
            )
        );

        const actions = document.createElement('div');
        actions.className = 'history-card-actions';

        const viewButton = document.createElement('button');
        viewButton.type = 'button';
        viewButton.className = 'history-view-button';
        viewButton.textContent = 'View Results';

        viewButton.addEventListener('click', function () {
            viewResults(item);
        });

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'history-delete-button';

        deleteButton.setAttribute('aria-label', 'Delete analysis');
        deleteButton.innerHTML = TRASH_ICON;

        deleteButton.addEventListener('click', function () {
            deleteItem(item, deleteButton);
        });

        actions.appendChild(viewButton);
        actions.appendChild(deleteButton);

        card.appendChild(actions);

        return card;
    }

    // =====================================
    // VIEW RESULTS
    // =====================================

    function viewResults(item) {
        try {
            const result = getAnalysisData(item);

            if (
                !result.analysis ||
                !Array.isArray(result.analysis.recommendations)
            ) {
                alert('AI analysis data could not be found.');
                return;
            }

            sessionStorage.setItem(
                ANALYSIS_KEY,
                JSON.stringify(result.data)
            );

            window.location.assign('/analysis');

        } catch (error) {
            console.error('Gagal membuka hasil analisis:', error);

            alert(
                'Hasil analisis tidak dapat dibuka. Silakan coba lagi.'
            );
        }
    }

    // =====================================
    // DELETE HISTORY
    // =====================================

    async function deleteItem(item, button) {
        const repositoryName =
            item.title ||
            item.repository ||
            item.data?.repository ||
            '';

        if (!window.confirm(
            'Hapus analisis "' + repositoryName + '" dari histori?'
        )) {
            return;
        }

        button.disabled = true;

        try {
            await deleteHistoryFromAPI(item);

            items = items.filter(function (entry) {
                return entry.id !== item.id;
            });

            render();

        } catch (error) {
            console.error('Gagal menghapus histori:', error);

            alert(
                error.message || 'Histori gagal dihapus. Silakan coba lagi.'
            );

            button.disabled = false;
        }
    }

    // =====================================
    // RENDER DAN SEARCH
    // =====================================

    function render() {
        const keyword = searchInput.value
            .trim()
            .toLowerCase();

        countBadge.textContent =
            items.length +
            (items.length === 1
                ? ' analysis in total'
                : ' analyses in total');

        list.replaceChildren();

        if (isLoading) {
            emptyState.hidden = true;
            noResult.hidden = true;

            list.appendChild(
                createText('p', 'history-no-result', 'Loading history...')
            );

            return;
        }

        if (items.length === 0) {
            emptyState.hidden = false;
            noResult.hidden = true;
            return;
        }

        emptyState.hidden = true;

        const filtered = items.filter(function (item) {
            const result = getAnalysisData(item);
            const priorityIssue = getPriorityIssue(result.analysis);

            const searchableText = [
                result.data.repository,
                item.repository,
                item.title,
                priorityIssue?.category,
                priorityIssue?.cluster,
                priorityIssue?.title,
                priorityIssue?.recommendation,
                priorityIssue?.description
            ].join(' ').toLowerCase();

            return searchableText.includes(keyword);
        });

        noResult.hidden = filtered.length > 0;

        filtered.forEach(function (item) {
            list.appendChild(createCard(item));
        });
    }

    searchInput.addEventListener('input', render);

    // =====================================
    // INITIALIZE
    // =====================================

    async function initializeHistory() {
        try {
            currentUser = await waitForUser();
            await loadHistory();

        } catch (error) {
            console.error('History initialization error:', error);

            isLoading = false;
            items = [];

            countBadge.textContent = '0 analyses in total';
            emptyState.hidden = true;
            noResult.hidden = true;

            showError(
                error.message || 'Silakan login kembali untuk melihat histori.'
            );
        }
    }

    initializeHistory();

});
