// Real local panel smoke test. Requires the isolated QA instance documented in VALIDATION.md.
import { chromium } from '@playwright/test';
import { mkdir, writeFile } from 'node:fs/promises';
const browser = await chromium.launch({
    headless: true,
    ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}),
});
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
const errors = [];
const base = process.env.VETRA_URL || 'http://127.0.0.1:8765';
page.on('pageerror', e => errors.push(e.message));
await mkdir('test-results', { recursive: true });
try {
    await page.goto(base, { waitUntil: 'networkidle' });
    console.log('URL', page.url());
    console.log((await page.locator('body').innerText()).slice(0, 1600));
    await page.screenshot({ path: 'test-results/01-login.png', fullPage: true });
    await writeFile('test-results/login.html', await page.content());
    if (process.env.VETRA_EMAIL && process.env.VETRA_PASSWORD) {
        const inputs = await page.locator('input').evaluateAll(list => list.map(x => ({ type: x.type, name: x.name, id: x.id })));
        console.log('Inputs', JSON.stringify(inputs));
        await page.locator('input[id="form.login"]').fill(process.env.VETRA_EMAIL);
        await page.locator('input[type="password"]').first().fill(process.env.VETRA_PASSWORD);
        await page.locator('button[type="submit"]').first().click();
        try {
            await page.waitForURL(url => !url.pathname.includes('login'), { timeout: 30000 });
        } catch (error) {
            console.log('LOGIN FAILURE', (await page.locator('body').innerText()).slice(0, 2200));
            console.log('JS ERRORS', JSON.stringify(errors));
            throw error;
        }
        await page.waitForLoadState('networkidle');
        await page.goto(base + '/admin/plugins', { waitUntil: 'networkidle' });
        console.log('Admin', (await page.locator('body').innerText()).slice(0, 2200));
        await page.screenshot({ path: 'test-results/02-admin-plugins.png', fullPage: true });
        await writeFile('test-results/admin.html', await page.content());
        await page.getByRole('button', { name: 'Ajustes', exact: true }).click();
        await page.getByText('Apariencia de VetraTheme', { exact: true }).waitFor();
        await page.waitForTimeout(350);
        console.log('SETTINGS', (await page.getByRole('dialog', { name: 'Ajustes', exact: true }).innerText()).slice(0, 2800));
        await page.screenshot({ path: 'test-results/03-settings.png', fullPage: true });
        await writeFile('test-results/settings.html', await page.content());
        await context.storageState({ path: 'test-results/session.json' });
    }
    console.log('PAGE_ERRORS', JSON.stringify(errors));
} catch (error) {
    await page.screenshot({ path: 'test-results/failure.png', fullPage: true });
    throw error;
} finally {
    await browser.close();
}
