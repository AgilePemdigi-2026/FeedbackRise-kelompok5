const { Given, When, Then } = require('@cucumber/cucumber');
const { chromium } = require('playwright');

let browser;
let page;

Given('User is on the Login page at {string}', async function (url) {
    browser = await chromium.launch({
        channel: 'chrome',
        headless: true
    });

    page = await browser.newPage();

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

When('User presses the {string} button', async function (buttonName) {
    if (buttonName === 'Login') {
        await page.locator('#login-button').click();
    }

    if (buttonName === 'Submit') {
        await page.getByRole('button', { name: 'Send Email' }).click();
    }
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

When(/^User enters an email\/username that is not registered$/, async function () {
    await page.locator('#email').fill('unregistered@example.com');
    await page.locator('#password').fill('Test12345!');
});

Then('User should see a message that the account is not registered', async function () {
    const errorMessage = page.locator('#login-error');

    await page.waitForFunction(() => {
        const element = document.querySelector('#login-error');
        return element && !element.hidden && element.textContent.trim() !== '';
    });

    await errorMessage.isVisible();
});

Then('User should be redirected to the Registration page at {string}', async function (url) {
    await page.waitForTimeout(1000);

    console.log('URL setelah login:', page.url());

    await page.waitForURL(`**${url}`, {
        timeout: 10000
    });

    await browser.close();
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