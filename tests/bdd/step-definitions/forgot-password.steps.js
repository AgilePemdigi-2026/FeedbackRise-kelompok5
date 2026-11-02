
const {
    Given,
    When,
    Then,
    After,
    setDefaultTimeout
} = require('@cucumber/cucumber');

const { chromium } = require('playwright');
const assert = require('assert');

setDefaultTimeout(30000);

let browser;
let page;

Given('User is on the Forgot Password page at {string}', async function (url) {
    browser = await chromium.launch({
        channel: 'chrome',
        headless: true
    });

    page = await browser.newPage();

    await page.goto(`http://localhost:8000${url}`);
});

When('User enters a registered email', async function () {
    await page.locator('#email').fill('dikalikarlina@gmail.com');
});

When('User leaves the email field empty', async function () {
    await page.locator('#email').fill('');
});

When('User enters an invalid email format', async function () {
    await page.locator('#email').fill('usergmail.com');
});

When('User presses the "Send Email" button', async function () {
    await page.getByRole('button', { name: 'Send Email' }).click();
});

When('Firebase encounters an error while sending the reset email', async function () {
    await page.route('**/identitytoolkit.googleapis.com/**', async route => {
        await route.fulfill({
            status: 400,
            contentType: 'application/json',
            body: JSON.stringify({
                error: {
                    code: 400,
                    message: 'INTERNAL_ERROR'
                }
            })
        });
    });

    await page.locator('#forgotForm button[type="submit"]').click();
});

Then('System should send a password reset email through Firebase', async function () {
    // Memastikan respons sukses ditampilkan melalui notifikasi aplikasi.
    await page.locator('#success.show').waitFor({ state: 'visible' });

    const message = await page.locator('#success').textContent();

    assert(
        message.includes('instruksi reset password akan dikirim'),
        'Konfirmasi reset password tidak ditemukan'
    );
});

Then('User should see a password reset confirmation message', async function () {
    await assertNotification();
});

Then('System should prevent the form submission', async function () {
    const email = page.locator('#email');

    const isValid = await email.evaluate(element => element.checkValidity());

    assert.strictEqual(isValid, false, 'Email seharusnya tidak valid');

});

Then('User should see an email validation message', async function () {
    const email = page.locator('#email');

    const validationMessage = await email.evaluate(
        element => element.validationMessage
    );

    assert(validationMessage.length > 0, 'Validasi email tidak muncul');

});

Then('User should see a password reset failure message', async function () {
    const success = page.locator('#success');

    await success.waitFor({ state: 'visible' });

    const message = await success.textContent();

    assert(
        message.includes('Gagal mengirim email reset password'),
        'Pesan kegagalan tidak sesuai'
    );
});

Then('User should remain on the Forgot Password page at {string}', async function (url) {
    assert(
        page.url().endsWith(url),
        'User tidak berada di halaman Forgot Password'
    );
});

async function assertNotification() {
    const success = page.locator('#success');

    await success.waitFor({ state: 'visible' });

    assert(await success.isVisible(), 'Notifikasi tidak muncul');
}

After(async function () {
    if (browser) {
        await browser.close();
        browser = null;
        page = null;
    }
});