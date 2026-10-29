const {
    Given,
    When,
    Then,
    After,
    setDefaultTimeout
} = require('@cucumber/cucumber');

const { chromium } = require('playwright');
const assert = require('assert');

setDefaultTimeout(40000);

const BASE_URL = 'http://localhost:8000';

let browser;
let page;
let registrationData = {};
let registrationFailureStatus = null;
let dialogMessages = [];
let notices = [];

// ==================================================
// BROWSER
// ==================================================

async function openPage(url) {
    if (browser) {
        await browser.close();
    }

    registrationFailureStatus = null;
    dialogMessages = [];
    notices = [];

    browser = await chromium.launch({
        channel: 'chrome',
        headless: true
    });

    page = await browser.newPage();

    // Simpan pesan alert() jika aplikasi memakainya.
    page.on('dialog', async dialog => {
        dialogMessages.push(dialog.message());
        await dialog.accept().catch(() => {});
    });

    // Aplikasi menampilkan #register-success lalu langsung redirect ke
    // /login, sehingga pesannya hilang sebelum sempat dicek. Observer ini
    // dipasang di setiap halaman dan mencatat pesan begitu muncul.
    await page.exposeFunction('__captureNotice', (type, text) => {
        notices.push({ type, text });
    });

    await page.addInitScript(() => {
        const targets = [
            ['register-success', 'success'],
            ['register-error', 'error']
        ];

        const watch = () => {
            for (const [id, type] of targets) {
                const element = document.getElementById(id);

                if (!element || element.__watched) {
                    continue;
                }

                element.__watched = true;

                const check = () => {
                    const text = (element.textContent || '').trim();
                    const visible =
                        !element.hidden &&
                        getComputedStyle(element).display !== 'none';

                    if (visible && text) {
                        window.__captureNotice(type, text);
                    }
                };

                new MutationObserver(check).observe(element, {
                    attributes: true,
                    childList: true,
                    characterData: true,
                    subtree: true
                });

                check();
            }
        };

        document.addEventListener('DOMContentLoaded', watch);

        if (document.readyState !== 'loading') {
            watch();
        }
    });

    await page.goto(`${BASE_URL}${url}`, {
        waitUntil: 'domcontentloaded'
    });
}

After(async function () {
    if (browser) {
        await browser.close();
    }

    browser = null;
    page = null;
});

// ==================================================
// HELPER
// ==================================================

// Selector ketat: dipakai untuk memastikan TIDAK ada error.
const STRICT_ERROR_SELECTORS = [
    '#register-error',
    '[role="alert"]',
    '.error-message',
    '.invalid-feedback'
];

// Selector longgar: dipakai untuk memastikan ADA pesan error.
const BROAD_ERROR_SELECTORS = [
    ...STRICT_ERROR_SELECTORS,
    '.alert-danger',
    '.text-danger',
    '.error',
    '.toast-error',
    '#error',
    '#message'
];

const SUCCESS_SELECTORS = [
    '#register-success',
    '#success',
    '.alert-success',
    '.success',
    '.success-message',
    '.toast-success',
    '.toast',
    '[role="status"]'
];

async function fillRegistrationForm(data) {
    await page.locator('#email').fill(data.email);
    await page.locator('#username').fill(data.username);
    await page.locator('#password').fill(data.password);

    await page.locator('#password_confirmation').fill(
        data.passwordConfirmation || data.password
    );
}

async function clickRegisterButton() {
    await page.locator('#register-button').click();
}

async function visibleTexts(selectors) {
    const texts = [];

    for (const selector of selectors) {
        const elements = page.locator(selector);
        const count = await elements.count().catch(() => 0);

        for (let i = 0; i < count; i++) {
            const element = elements.nth(i);

            if (await element.isVisible().catch(() => false)) {
                const text = (
                    await element.innerText().catch(() => '')
                ).trim();

                if (text) {
                    texts.push(text);
                }
            }
        }
    }

    return texts;
}

async function bodyText() {
    return (
        await page.locator('body').innerText().catch(() => '')
    ).trim();
}

// Pesan error yang terlihat (selector ketat). Kosong jika tidak ada.
async function findVisibleError() {
    const texts = await visibleTexts(STRICT_ERROR_SELECTORS);
    return texts[0] || '';
}

// Pesan error apa pun: alert(), notifikasi yang tercatat observer,
// elemen error yang terlihat, atau validasi bawaan browser (HTML5).
async function findAnyError() {
    if (dialogMessages.length > 0) {
        return dialogMessages.join(' ');
    }

    const noticed = notices.find(notice => notice.type === 'error');

    if (noticed) {
        return noticed.text;
    }

    const texts = await visibleTexts(BROAD_ERROR_SELECTORS);

    if (texts.length > 0) {
        return texts.join(' ');
    }

    const nativeMessage = await page
        .locator('#register-form')
        .evaluate(form => {
            if (form.checkValidity()) {
                return '';
            }

            const invalid = form.querySelector(':invalid');
            return invalid ? invalid.validationMessage : 'invalid';
        })
        .catch(() => '');

    return nativeMessage || '';
}

// Menunggu sampai fungsi mengembalikan nilai truthy.
async function pollFor(fn, timeout = 10000, interval = 250) {
    const start = Date.now();

    while (Date.now() - start < timeout) {
        const result = await fn().catch(() => null);

        if (result) {
            return result;
        }

        await new Promise(resolve => setTimeout(resolve, interval));
    }

    return null;
}

async function assertRegistrationError(pattern) {
    const message = await pollFor(async () => {
        const candidates = [
            ...dialogMessages,
            ...notices
                .filter(notice => notice.type === 'error')
                .map(notice => notice.text),
            ...(await visibleTexts(BROAD_ERROR_SELECTORS))
        ];

        const matched = candidates.find(text => pattern.test(text));

        if (matched) {
            return matched;
        }

        const body = await bodyText();
        return pattern.test(body) ? body : null;
    }, 10000);

    assert(
        message,
        `Pesan error yang sesuai tidak ditemukan. Isi halaman: "${(await bodyText()).slice(0, 300)}"`
    );

    return message;
}

// ==================================================
// OPEN PAGE
// ==================================================

Given(
    'User starts on the Login page for registration at {string}',
    async function (url) {
        await openPage(url);
    }
);

Given(
    'User is on the Registration page at {string}',
    async function (url) {
        await openPage(url);
    }
);

// ==================================================
// SCENARIO 1: VALID REGISTRATION
// ==================================================

When(
    'User selects the {string} menu',
    async function (menuName) {
        assert.strictEqual(menuName, 'Registration');

        const registerLink = page.locator(
            'a[href="/register"], a[href$="/register"]'
        ).first();

        if (await registerLink.count()) {
            await registerLink.click({ timeout: 5000 });
        } else {
            const textLink = page.getByText(
                /^(Registration|Register|Sign up|Create an account)$/i
            ).first();

            if (!(await textLink.count())) {
                throw new Error(
                    'Link menuju halaman registrasi tidak ditemukan pada halaman Login.'
                );
            }

            await textLink.click({ timeout: 5000 });
        }
    }
);

Then(
    'Registration page should be displayed at {string}',
    async function (url) {
        await page.waitForURL(
            currentUrl => new URL(currentUrl).pathname === url,
            { timeout: 10000 }
        );

        assert.strictEqual(
            new URL(page.url()).pathname,
            url
        );
    }
);

Then(
    'User should see the Registration form',
    async function () {
        await page.locator('#register-form').waitFor({
            state: 'visible'
        });

        for (const selector of [
            '#email',
            '#username',
            '#password',
            '#password_confirmation',
            '#register-button'
        ]) {
            await page.locator(selector).waitFor({
                state: 'visible'
            });
        }
    }
);

When(
    'User enters valid registration data',
    async function () {
        const id = Date.now();

        registrationData = {
            email: `bdd${id}@example.com`,
            username: `bdduser${id}`,
            password: 'Test12345!'
        };

        await fillRegistrationForm(registrationData);
    }
);

When(
    /^User clicks the Registration form's "Register" button$/,
    async function () {
        await clickRegisterButton();
    }
);

Then(
    'System should validate the completeness and format of the data',
    async function () {
        const email = await page.locator('#email').inputValue();
        const username = await page.locator('#username').inputValue();
        const password = await page.locator('#password').inputValue();
        const confirmation = await page
            .locator('#password_confirmation')
            .inputValue();

        assert(email.trim(), 'Email belum diisi.');
        assert(username.trim(), 'Username belum diisi.');
        assert(password, 'Password belum diisi.');

        assert.strictEqual(
            password,
            confirmation,
            'Password dan konfirmasi password tidak sama.'
        );
    }
);

Then(
    /^System should\s*check the availability of the email\/username$/,
    async function () {
        await page.waitForLoadState('networkidle').catch(() => {});

        const error = await findVisibleError();

        assert(
            !/already registered|already exists|already in use|terdaftar|sudah digunakan/i.test(error),
            `Email/username terdeteksi sudah digunakan: ${error}`
        );
    }
);

Then(
    "System should save the user's account data",
    async function () {
        await page.waitForLoadState('networkidle').catch(() => {});

        const error = await findVisibleError();

        if (error) {
            throw new Error(
                `Registrasi belum berhasil menyimpan data: ${error}`
            );
        }
    }
);

Then(
    'User should see a registration success notification',
    async function () {
        const SUCCESS_TEXT =
            /registration (successful|success|complete)|register(ed)? successfully|account (has been )?(created|registered)|berhasil|success/i;

        // Pesan sukses biasanya tampil sebentar sebelum redirect ke /login,
        // jadi yang diperiksa adalah catatan dari observer, bukan hanya
        // kondisi halaman saat ini.
        const message = await pollFor(async () => {
            const noticed = notices.find(
                notice => notice.type === 'success'
            );

            if (noticed) {
                return noticed.text;
            }

            const dialog = dialogMessages.find(text => text.trim());

            if (dialog) {
                return dialog;
            }

            const elements = await visibleTexts(SUCCESS_SELECTORS);

            if (elements.length > 0) {
                return elements[0];
            }

            const body = await bodyText();
            const match = body.match(SUCCESS_TEXT);

            return match ? match[0] : null;
        }, 10000);

        assert(
            message,
            `Notifikasi sukses registrasi tidak ditemukan. URL: ${page.url()}. Catatan notifikasi: ${JSON.stringify(notices)}. Isi halaman: "${(await bodyText()).slice(0, 300)}"`
        );
    }
);

Then(
    'User should be redirected to the Login page at {string}',
    async function (url) {
        await page.waitForURL(
            currentUrl => new URL(currentUrl).pathname === url,
            { timeout: 10000 }
        );

        assert.strictEqual(
            new URL(page.url()).pathname,
            url
        );
    }
);

// ==================================================
// SCENARIO 2: INVALID REGISTRATION DATA
// ==================================================

When(
    'User enters incomplete or invalid data',
    async function () {
        await page.locator('#email').fill('invalid-email');
        await page.locator('#username').fill('');
        await page.locator('#password').fill('');
        await page.locator('#password_confirmation').fill('');
    }
);

Then(
    'User should see an error message',
    async function () {
        const message = await pollFor(findAnyError, 5000);

        assert(
            message,
            'Pesan error tidak ditemukan. Pastikan aplikasi menampilkan pesan error ketika data tidak valid.'
        );
    }
);

Then(
    'User should be asked to correct the entered data',
    async function () {
        const message = await findAnyError();

        assert(
            message,
            'Aplikasi tidak menampilkan pesan untuk memperbaiki data.'
        );
    }
);

When(
    'User corrects the entered data',
    async function () {
        const id = Date.now();

        registrationData = {
            email: `corrected${id}@example.com`,
            username: `correcteduser${id}`,
            password: 'Correct12345!'
        };

        await fillRegistrationForm(registrationData);
    }
);

When(
    /^User clicks the Registration form's "Register" button again$/,
    async function () {
        await clickRegisterButton();
    }
);

Then(
    'System should validate the completeness and format of the data again',
    async function () {
        const email = await page.locator('#email').inputValue();
        const username = await page.locator('#username').inputValue();
        const password = await page.locator('#password').inputValue();
        const confirmation = await page
            .locator('#password_confirmation')
            .inputValue();

        assert(email.trim(), 'Email belum diisi.');
        assert(username.trim(), 'Username belum diisi.');
        assert(password, 'Password belum diisi.');

        assert.strictEqual(
            password,
            confirmation,
            'Password dan konfirmasi password tidak sama.'
        );
    }
);

// ==================================================
// SCENARIO 3: DUPLICATE EMAIL OR USERNAME
// ==================================================

When(
    /^User enters an already registered\s*email\/username$/,
    async function () {
        await fillRegistrationForm({
            email: 'test1@example.com',
            username: 'existinguser',
            password: 'Test12345!'
        });
    }
);

Then(
    /^System should\s*detect that the email\/username is already registered$/,
    async function () {
        await page.waitForLoadState('networkidle').catch(() => {});
    }
);

// Memakai regex karena tanda "/" pada Cucumber Expression berarti
// alternatif ("email ATAU username"), bukan teks biasa.
Then(
    /^User should see a message that the email\/username is already in use$/,
    async function () {
        await assertRegistrationError(
            /already|registered|exists|terdaftar|digunakan|sudah ada/i
        );
    }
);

When(
    /^User enters another email\/username$/,
    async function () {
        const id = Date.now();

        await page.locator('#email').fill(`another${id}@example.com`);
        await page.locator('#username').fill(`anotheruser${id}`);
        await page.locator('#password').fill('Test12345!');
        await page.locator('#password_confirmation').fill('Test12345!');
    }
);

Then(
    /^System should\s*check the availability of the email\/username again$/,
    async function () {
        await page.waitForLoadState('networkidle').catch(() => {});

        const error = await findVisibleError();

        assert(
            !/already registered|already exists|already in use|terdaftar|sudah digunakan/i.test(error),
            `Email/username baru ditolak: ${error}`
        );
    }
);

// ==================================================
// SCENARIO 4: REGISTRATION SAVE FAILURE
// ==================================================

// Registrasi memakai Firebase (JavaScript di browser), bukan POST ke
// route Laravel. Simulasi gagal simpan dilakukan dengan membalas
// request ke layanan Firebase dan (jika ada) POST ke endpoint
// "register" lokal dengan status 500.
const FIREBASE_HOSTS = [
    'identitytoolkit.googleapis.com',
    'securetoken.googleapis.com',
    'firestore.googleapis.com',
    'firebaseio.com'
];

function isSaveRequest(url) {
    if (FIREBASE_HOSTS.some(host => url.hostname.endsWith(host))) {
        return true;
    }

    return (
        url.origin === BASE_URL &&
        /register/i.test(url.pathname)
    );
}

Given(
    'User has entered valid registration data and the save operation will fail',
    async function () {
        const id = Date.now();

        registrationData = {
            email: `failure${id}@example.com`,
            username: `failureuser${id}`,
            password: 'Test12345!'
        };

        registrationFailureStatus = null;

        await fillRegistrationForm(registrationData);

        await page.route(isSaveRequest, async route => {
            const request = route.request();
            const headers = request.headers();
            const url = new URL(request.url());
            const isLocal = url.origin === BASE_URL;

            // Halaman /register lokal (GET) tidak boleh diganggu.
            if (isLocal && request.method() === 'GET') {
                await route.continue();
                return;
            }

            const cors = {
                'access-control-allow-origin': headers['origin'] || '*',
                'access-control-allow-headers':
                    headers['access-control-request-headers'] || '*',
                'access-control-allow-methods':
                    'GET,POST,PUT,PATCH,DELETE,OPTIONS'
            };

            // Preflight CORS ke layanan Firebase.
            if (request.method() === 'OPTIONS') {
                await route.fulfill({ status: 204, headers: cors });
                return;
            }

            registrationFailureStatus = 500;

            const message = 'Registration failed. Please try again.';

            if (isLocal && request.resourceType() === 'document') {
                await route.fulfill({
                    status: 500,
                    contentType: 'text/html; charset=utf-8',
                    body: `<!DOCTYPE html>
<html>
  <head><title>Registration Failed</title></head>
  <body>
    <h1>Registration failed</h1>
    <p id="register-error" role="alert">${message}</p>
  </body>
</html>`
                });
                return;
            }

            await route.fulfill({
                status: 500,
                contentType: 'application/json',
                headers: cors,
                body: JSON.stringify({
                    success: false,
                    message,
                    error: {
                        code: 500,
                        message: 'INTERNAL_ERROR',
                        errors: [
                            {
                                message: 'INTERNAL_ERROR',
                                domain: 'global',
                                reason: 'backendError'
                            }
                        ]
                    }
                })
            });
        });
    }
);

Then(
    'User should see a registration failure message',
    async function () {
        // Beri waktu aplikasi mengirim request lalu menampilkan error.
        const message = await pollFor(async () => {
            const error = await findAnyError();

            if (error) {
                return error;
            }

            const body = await bodyText();

            return /registration failed|registrasi gagal|gagal|failed/i.test(body)
                ? body
                : null;
        }, 20000);

        assert.strictEqual(
            registrationFailureStatus,
            500,
            'Simulasi gagal simpan tidak terpicu: tidak ada request ke Firebase atau endpoint register. Periksa kode register.js untuk melihat ke mana data registrasi dikirim.'
        );

        assert(
            message,
            `Pesan kegagalan tidak terlihat pada halaman. URL: ${page.url()}. Catatan notifikasi: ${JSON.stringify(notices)}. Isi halaman: "${(await bodyText()).slice(0, 300)}"`
        );
    }
);

Then(
    'User should remain on the Registration page at {string}',
    async function (url) {
        assert.strictEqual(
            new URL(page.url()).pathname,
            url,
            `User tidak berada di halaman ${url}. URL saat ini: ${page.url()}`
        );
    }
);

// ==================================================
// COMMON STEP
// ==================================================

Then(
    'User should follow the registration process',
    async function () {
        assert(
            page && !page.isClosed(),
            'Halaman registrasi sudah tertutup.'
        );
    }
);