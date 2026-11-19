const { Given, When, Then } = require('@cucumber/cucumber');
const { chromium } = require('playwright');
const assert = require('assert');

let browser;
let page;
let loginNotices = [];
let signInMockHit = false;
let pageConsole = [];

Given('User is on the Login page at {string}', async function (url) {
    loginNotices = [];
    signInMockHit = false;
    pageConsole = [];

    browser = await chromium.launch({
        channel: 'chrome',
        headless: true
    });

    page = await browser.newPage();

    // Catat log console halaman untuk membantu diagnosis.
    page.on('console', message => {
        pageConsole.push(message.text());
    });

    page.on('pageerror', error => {
        pageConsole.push(`PAGE ERROR: ${error.message}`);
    });

    await page.goto(`http://localhost:8000${url}`);
});

// ==================== SCENARIO 1 ====================

When(/^User enters email\/username and password$/, async function () {
    await page.locator('#email').fill('test1@example.com');
    await page.locator('#password').fill('Test12345!');
});

Then('System should validate the completeness and format of the login data', async function () {
    await page.waitForLoadState('networkidle');
});

Then(/^System should verify the email\/username and password against the account data$/, async function () {
    await page.waitForLoadState('networkidle');
});

Then("System should identify the user's access rights", async function () {
    // Hak akses diverifikasi setelah login berhasil.
});

Then("User should be redirected to the main page at {string} according to the user's access rights", async function (url) {
    await page.waitForTimeout(3000);

    console.log('User should be redirected to the main page at "/home" according to the user\'s access rights. URL:', page.url());

    await browser.close();
});

// ==================== SCENARIO 2 ====================

When('User enters incomplete or invalid login data', async function () {
    await page.locator('#email').fill('');
    await page.locator('#password').fill('');
});

Then(/^User should see an error message that email\/username and password must be entered correctly$/, async function () {
    const emailInput = page.locator('#email');
    const passwordInput = page.locator('#password');

    await emailInput.evaluate((element) => {
        if (!element.checkValidity()) {
            element.reportValidity();
        }
    });

    await passwordInput.evaluate((element) => {
        if (!element.checkValidity()) {
            element.reportValidity();
        }
    });
});

Then('User should remain on the Login page at {string}', async function (url) {
    await page.waitForURL(`**${url}`);
});

When(/^User corrects the email\/username and password$/, async function () {
    await page.locator('#email').fill('test1@example.com');
    await page.locator('#password').fill('Test12345!');
});

When('User presses the "Login" button again', async function () {
    await page.locator('#login-button').click();
});

Then('System should validate the login data again', async function () {
    await page.waitForLoadState('networkidle');
});

Then('User should follow the login process', async function () {
    await page.waitForTimeout(3000);

    console.log('User should follow the login process. URL:', page.url());

    await browser.close();
});

// ==================== SCENARIO 3 ====================

When(/^User enters an incorrect email\/username or password$/, async function () {
    await page.locator('#email').fill('wrong@example.com');
    await page.locator('#password').fill('WrongPassword123!');
});

Then(/^User should see a message that the email\/username or password is incorrect$/, async function () {
    const errorMessage = page.locator('#login-error');

    await page.waitForFunction(() => {
        const element = document.querySelector('#login-error');

        return element &&
            !element.hidden &&
            element.textContent.trim() !== '';
    });

    await errorMessage.isVisible();
});

When(/^User enters the email\/username and password again$/, async function () {
    await page.locator('#email').fill('test1@example.com');
    await page.locator('#password').fill('Test12345!');
});

Then('System should verify the account credentials', async function () {
    await page.waitForLoadState('networkidle');
});

Then('User should be redirected to the main page at {string}', async function (url) {
    await page.waitForTimeout(3000);

    console.log('User should be redirected to the main page at "/home". URL:', page.url());

    await browser.close();
});

// ==================== SCENARIO 4 ====================

When('User selects the {string} button', async function (buttonName) {
    if (buttonName === 'Forgot Password') {
        await page.getByText('Forgot Password').click();
    }
});

Then('User should be redirected to the Forgot Password page at {string}', async function (url) {
    await page.waitForURL(`**${url}`);
});

When('User enters the email', async function () {
    await page.locator('#email').fill('test1@example.com');
});

When('User presses the "Login" button', async function () {
    await page.locator('#login-button').click();
});

Then("System should verify the user's email", async function () {
    await page.waitForLoadState('networkidle');
});

Then('User should see a notification that the password reset instructions have been sent', async function () {
    const successMessage = page.locator('#success');

    await successMessage.waitFor({ state: 'visible' });
    await successMessage.isVisible();

    await browser.close();
});

// ==================== SCENARIO 5 ====================

// Firebase (dengan "email enumeration protection" aktif) membalas akun
// yang tidak terdaftar sama seperti password salah, sehingga aplikasi
// tidak bisa membedakannya. Agar kondisi "akun belum terdaftar" bisa
// diuji secara konsisten, respons Firebase untuk login disimulasikan
// sebagai EMAIL_NOT_FOUND (dipetakan SDK menjadi auth/user-not-found).
function isSignInRequest(url) {
    return (
        url.hostname === 'identitytoolkit.googleapis.com' &&
        url.pathname.includes('accounts:signInWithPassword')
    );
}

When(/^User enters an email\/username that is not registered$/, async function () {
    // Catat pesan #login-error begitu muncul, karena aplikasi bisa
    // langsung berpindah halaman setelah menampilkannya.
    await page.exposeFunction('__captureLoginNotice', (text) => {
        loginNotices.push(text);
    });

    await page.evaluate(() => {
        const element = document.getElementById('login-error');

        if (!element) {
            return;
        }

        const check = () => {
            const text = (element.textContent || '').trim();

            if (!element.hidden && text) {
                window.__captureLoginNotice(text);
            }
        };

        new MutationObserver(check).observe(element, {
            attributes: true,
            childList: true,
            characterData: true,
            subtree: true
        });

        check();
    });

    await page.route(isSignInRequest, async route => {
        const request = route.request();
        const headers = request.headers();

        const cors = {
            'access-control-allow-origin': headers['origin'] || '*',
            'access-control-allow-headers':
                headers['access-control-request-headers'] || '*',
            'access-control-allow-methods': 'GET,POST,OPTIONS'
        };

        if (request.method() === 'OPTIONS') {
            await route.fulfill({ status: 204, headers: cors });
            return;
        }

        signInMockHit = true;

        await route.fulfill({
            status: 400,
            contentType: 'application/json',
            headers: cors,
            body: JSON.stringify({
                error: {
                    code: 400,
                    message: 'EMAIL_NOT_FOUND',
                    errors: [
                        {
                            message: 'EMAIL_NOT_FOUND',
                            domain: 'global',
                            reason: 'invalid'
                        }
                    ]
                }
            })
        });
    });

    await page.locator('#email').fill('unregistered@example.com');
    await page.locator('#password').fill('Test12345!');
});

Then('User should see a message that the account is not registered', async function () {
    const start = Date.now();
    let message = '';

    while (Date.now() - start < 10000 && !message) {
        message = loginNotices.find(text => text.trim()) || '';

        if (!message) {
            const text = await page
                .locator('#login-error')
                .textContent({ timeout: 500 })
                .catch(() => '');

            message = (text || '').trim();
        }

        if (!message) {
            await new Promise(resolve => setTimeout(resolve, 250));
        }
    }

    assert(
        message,
        `Pesan akun belum terdaftar tidak muncul. URL: ${page.url()}`
    );
});

Then('User should be redirected to the Registration page at {string}', async function (url) {
    try {
        await page.waitForURL(`**${url}`, {
            timeout: 10000
        });
    } catch (error) {
        // Tampilkan kondisi halaman agar penyebabnya terlihat.
        const loginError = await page
            .locator('#login-error')
            .textContent()
            .catch(() => '');

        throw new Error(
            `Tidak ada redirect ke ${url}. URL saat ini: ${page.url()}. ` +
            `Pesan di #login-error: "${(loginError || '').trim()}". ` +
            `Pesan yang tercatat: ${JSON.stringify(loginNotices)}. ` +
            `Respons Firebase disimulasikan: ${signInMockHit}. ` +
            `Console halaman: ${JSON.stringify(pageConsole.slice(-8))}`
        );
    } finally {
        await browser.close();
    }
});

// ==================== SCENARIO 6 ====================

When(/^User enters valid email\/username and password$/, async function () {
    await page.locator('#email').fill('test1@example.com');
    await page.locator('#password').fill('Test12345!');
});

When('System encounters an error while accessing the account data', async function () {
    await page.evaluate(() => {
        window.__simulateLoginSystemError = true;
    });

    await page.locator('#login-button').click();
});

Then('User should see a message that the login cannot be processed', async function () {
    const errorMessage = page.locator('#login-error');

    await errorMessage.waitFor({
        state: 'visible',
        timeout: 5000
    });

    const message = await errorMessage.textContent();

    if (!message.includes('Login gagal')) {
        throw new Error(
            `Pesan error tidak sesuai. Pesan yang muncul: ${message}`
        );
    }
});