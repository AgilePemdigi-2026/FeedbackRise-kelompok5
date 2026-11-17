
const {
    Before,
    After,
    Given,
    When,
    Then,
    setDefaultTimeout
} = require('@cucumber/cucumber');

const { chromium, expect } = require('@playwright/test');

const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:8000';

setDefaultTimeout(90000);

// ========================================
// BROWSER SETUP
// ========================================

Before(async function () {
    this.browser = await chromium.launch({
        headless: true
    });

    this.context = await this.browser.newContext();
    this.page = await this.context.newPage();

    this.apiResponse = null;
    this.apiResponseStatus = null;
    this.apiResponseData = null;
    this.apiResponseText = null;

    // Promise untuk menerima response langsung dari browser.
    this.apiResponsePromise = new Promise((resolve) => {
        this.resolveApiResponse = resolve;
    });

    // Binding mengirim data response ke Node.js.
    await this.page.exposeBinding(
        '__captureGitHubIssueResponse',
        async ({}, payload) => {
            this.apiResponse = payload;
            this.apiResponseStatus = payload.status;
            this.apiResponseText = payload.text || '';

            try {
                this.apiResponseData = payload.text
                    ? JSON.parse(payload.text)
                    : null;
            } catch (error) {
                this.apiResponseData = null;
                this.apiResponseText +=
                    '\nJSON parsing error: ' + error.message;
            }

            console.log('\n========== GITHUB ISSUE API RESPONSE ==========');
            console.log('HTTP Status:', this.apiResponseStatus);
            console.log('Response Body:', this.apiResponseText);
            console.log('================================================\n');

            this.resolveApiResponse(payload);
        }
    );

    // Intercept fetch dan kirim salinan response ke Node.js.
    await this.page.addInitScript(() => {
        const originalFetch = window.fetch;

        window.fetch = async function (...args) {
            const response = await originalFetch.apply(this, args);

            const requestUrl = args[0] instanceof Request
                ? args[0].url
                : String(args[0]);

            const method = args[1]?.method ||
                (args[0] instanceof Request
                    ? args[0].method
                    : 'GET');

            if (
                requestUrl.includes('/api/github/issues') &&
                method.toUpperCase() === 'POST'
            ) {
                try {
                    const responseText = await response.clone().text();

                    await window.__captureGitHubIssueResponse({
                        status: response.status,
                        text: responseText
                    });

                } catch (error) {
                    await window.__captureGitHubIssueResponse({
                        status: response.status,
                        text: null,
                        error: error.message
                    });
                }
            }

            return response;
        };
    });
});

After(async function () {
    if (this.browser) {
        await this.browser.close();
    }
});

// ========================================
// HELPER
// ========================================

function escapeRegex(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// ========================================
// BACKGROUND
// ========================================

Given('the user is on the Home page at {string}', async function (path) {
    await this.page.goto(`${BASE_URL}${path}`);

    await expect(this.page).toHaveURL(
        new RegExp(`${escapeRegex(path)}$`)
    );
});

// ========================================
// GIVEN
// ========================================

Given('the GitHub Issue input field is displayed', async function () {
    await expect(
        this.page.locator('#issueUrl')
    ).toBeVisible();
});

// ========================================
// WHEN - INPUT
// ========================================

When('the user enters a valid GitHub repository Issues URL', async function () {
    await this.page.locator('#issueUrl').fill(
        'https://github.com/microsoft/vscode/issues'
    );

    const issueLimit = this.page.locator('#issueLimit');

    if (await issueLimit.count()) {
        await issueLimit.fill('3');
    }
});

When('the user leaves the GitHub Issue input empty', async function () {
    await this.page.locator('#issueUrl').fill('');
});

When('the user enters a valid GitHub repository Issues URL that cannot be processed', async function () {
    await this.page.locator('#issueUrl').fill(
        'https://github.com/feedbackrise-bdd-test/nonexistent-repository/issues'
    );

    const issueLimit = this.page.locator('#issueLimit');

    if (await issueLimit.count()) {
        await issueLimit.fill('3');
    }
});

// ========================================
// WHEN - CLICK ANALYZE
// ========================================

When('the user clicks the {string} button', async function (buttonName) {
    const button = this.page.locator('#analyzeButton');

    await expect(button).toBeVisible();

    const inputValue = await this.page
        .locator('#issueUrl')
        .inputValue();

    // Input kosong.
    if (buttonName === 'Analyze' && inputValue.trim() === '') {
        await button.click();
        return;
    }

    // Klik dan tunggu response API.
    const responsePromise = this.apiResponsePromise;

    await button.click();

    // Tidak menunggu navigasi karena skenario gagal
    // memang harus tetap berada di halaman Home.
    await responsePromise;
});

// ========================================
// THEN - SUCCESS SCENARIO
// ========================================

Then('the system retrieves GitHub Issue information', async function () {
    expect(this.apiResponse).not.toBeNull();

    expect(
        this.apiResponseStatus,
        `GitHub API gagal: ${this.apiResponseText}`
    ).toBe(200);

    expect(
        this.apiResponseData,
        `Respons API tidak valid: ${this.apiResponseText}`
    ).not.toBeNull();

    expect(this.apiResponseData).toHaveProperty('issues');

    expect(
        Array.isArray(this.apiResponseData.issues)
    ).toBe(true);

    expect(
        this.apiResponseData.issues.length
    ).toBeGreaterThan(0);
});

Then('the system processes the issues using AI Service', async function () {
    expect(this.apiResponseData).not.toBeNull();

    expect(this.apiResponseData).toHaveProperty('analysis');

    expect(
        this.apiResponseData.analysis
    ).toBeTruthy();
});

Then('the system generates issue analysis and development recommendations', async function () {
    const recommendations =
        this.apiResponseData.analysis.recommendations;

    expect(Array.isArray(recommendations)).toBe(true);

    expect(recommendations.length).toBeGreaterThan(0);
});

Then('the user is redirected to {string}', async function (path) {
    await this.page.waitForURL(`**${path}`, {
        timeout: 30000
    });

    await expect(this.page).toHaveURL(
        new RegExp(`${escapeRegex(path)}$`)
    );
});

Then('the analysis results are displayed', async function () {
    await expect(
        this.page.locator('#analysisDetails')
    ).toBeVisible();

    await expect(
        this.page.locator(
            '#topRecommendations .top-recommendation-card'
        ).first()
    ).toBeVisible();

    await expect(
        this.page.locator('#detailTableBody tr').first()
    ).toBeVisible();
});

// ========================================
// THEN - EMPTY INPUT SCENARIO
// ========================================

Then('the browser displays a required field validation message', async function () {
    const validationMessage = await this.page
        .locator('#issueUrl')
        .evaluate(element => element.validationMessage);

    expect(validationMessage).not.toBe('');
});

Then('the form submission is prevented', async function () {
    const isInvalid = await this.page
        .locator('#issueUrl')
        .evaluate(element => element.validity.valueMissing);

    expect(isInvalid).toBe(true);
});

Then('the user remains on {string}', async function (path) {
    await expect(this.page).toHaveURL(
        new RegExp(`${escapeRegex(path)}$`)
    );
});

// ========================================
// THEN - FAILURE SCENARIO
// ========================================

Then('the system displays an error message', async function () {
    expect(this.apiResponse).not.toBeNull();

    expect(
        this.apiResponseStatus,
        `Seharusnya API mengembalikan error. Response: ${this.apiResponseText}`
    ).not.toBe(200);

    const status = this.page.locator('#analysisStatus');

    await expect(status).toBeVisible();

    await expect(status).not.toBeEmpty();
});

Then('the Analyze button is enabled again', async function () {
    await expect(
        this.page.locator('#analyzeButton')
    ).toBeEnabled();
});
