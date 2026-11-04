import { chromium, expect } from '@playwright/test';
import { readFile, writeFile } from 'node:fs/promises';
const browser = await chromium.launch({ headless: true, ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}) });
const context = await browser.newContext({ storageState: 'test-results/session.json', viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));
const base = process.env.VETRA_URL || 'http://127.0.0.1:8765';
async function settings() {
    await page.goto(base + '/admin/plugins', { waitUntil: 'networkidle' });
    await page.getByRole('button', { name: 'Settings', exact: true }).click();
    await page.getByText('Vetra Theme appearance', { exact: true }).waitFor();
    await page.waitForTimeout(350);
}
try {
    await settings();
    await page.locator('[id="mountedActionSchema0.opacity"]').fill('86');
    await page.locator('[id="mountedActionSchema0.notice"]').fill('QA: persistent settings · no Wings connection');
    await page.getByRole('button', { name: 'Submit', exact: true }).click();
    await page.getByText('Appearance saved', { exact: true }).waitFor();
    await settings();
    await expect(page.locator('[id="mountedActionSchema0.opacity"]')).toHaveValue('86');
    await expect(page.locator('[id="mountedActionSchema0.notice"]')).toHaveValue('QA: persistent settings · no Wings connection');
    await page.screenshot({ path: 'test-results/03-settings.png', fullPage: true });
    await page.locator('[id="mountedActionSchema0.restore_defaults"]').click();
    await page.getByRole('button', { name: 'Submit', exact: true }).click();
    await page.getByText('Appearance saved', { exact: true }).waitFor();
    await settings();
    await expect(page.locator('[id="mountedActionSchema0.opacity"]')).toHaveValue('82');
    await expect(page.locator('[id="mountedActionSchema0.notice"]')).toHaveValue('');
    await page.getByRole('button', { name: 'Cancel', exact: true }).click();
    console.log('PASS: settings saved, survived reload and restored');
    const path = (await readFile(process.env.VETRA_SERVER_PATH || '../.tools/server-path.txt', 'utf8')).trim();
    for (const [name, url] of [['servers', '/'], ['console', path]]) {
        const response = await page.goto(base + url, { waitUntil: 'networkidle' });
        console.log(name, response.status(), (await page.locator('body').innerText()).slice(-1300));
        await page.screenshot({ path: `test-results/04-${name}-dark.png`, fullPage: true });
        await writeFile(`test-results/${name}.html`, await page.content());
        await expect(page.locator('#vetra-ambient')).toHaveCount(1);
        if (name === 'console') await expect(page.locator('#terminal')).toBeVisible();
        // Filament's documented native theme store; no mock application state.
        await page.evaluate(() => window.Alpine.store('theme', 'light'));
        await page.waitForTimeout(300);
        await page.screenshot({ path: `test-results/05-${name}-light.png`, fullPage: true });
        await page.evaluate(() => window.Alpine.store('theme', 'dark'));
        await page.setViewportSize({ width: 390, height: 844 });
        await page.waitForTimeout(350);
        await page.screenshot({ path: `test-results/06-${name}-mobile.png`, fullPage: true });
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth > innerWidth + 2);
        if (overflow) throw new Error(`${name}: mobile horizontal overflow`);
        await page.setViewportSize({ width: 1440, height: 1000 });
    }
    console.log('PAGE_ERRORS', JSON.stringify(errors));
    await writeFile('test-results/browser-report.json', JSON.stringify({ settingsPersistence: true, restore: true, mobileOverflow: false, errors }, null, 2));
} catch (error) {
    console.log((await page.locator('body').innerText()).slice(-2500));
    await page.screenshot({ path: 'test-results/failure-settings.png', fullPage: true });
    throw error;
} finally { await browser.close(); }
