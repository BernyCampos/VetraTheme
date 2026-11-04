import { chromium, expect } from '@playwright/test';
import { readFile, writeFile } from 'node:fs/promises';
const browser = await chromium.launch({ headless: true, ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}) });
const context = await browser.newContext({ storageState: 'test-results/session.json' });
const page = await context.newPage();
const base = process.env.VETRA_URL || 'http://127.0.0.1:8765';
async function settings() {
    await page.goto(base + '/admin/plugins', { waitUntil: 'networkidle' });
    await page.getByRole('button', { name: 'Settings', exact: true }).click();
    await page.getByText('Vetra Theme appearance', { exact: true }).waitFor();
}
const results = [];
try {
    await settings();
    const webp = Buffer.from(await page.evaluate(() => {
        const canvas = document.createElement('canvas'); canvas.width = 16; canvas.height = 16;
        canvas.getContext('2d').fillRect(0, 0, 16, 16);
        return canvas.toDataURL('image/webp').split(',')[1];
    }), 'base64');
    await writeFile('tests/fixtures/background.webp', webp);
    await page.getByRole('button', { name: 'Cancel', exact: true }).click();
    const svg = Buffer.from('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"><rect width="16" height="16" fill="#739fe3"/></svg>');
    for (const [extension, mime, buffer] of [['gif', 'image/gif', await readFile('tests/fixtures/background.gif')], ['webp', 'image/webp', webp], ['svg', 'image/svg+xml', svg]]) {
        await settings();
        await page.locator('input[type=file]').setInputFiles({ name: `background.${extension}`, mimeType: mime, buffer });
        await expect(page.locator('.filepond--item[data-filepond-item-state="processing-complete"]')).toHaveCount(1, { timeout: 20000 });
        await page.getByRole('button', { name: 'Submit', exact: true }).click();
        await page.getByText('Appearance saved', { exact: true }).waitFor({ timeout: 30000 });
        await settings();
        const path = await page.locator('[id="mountedActionSchema0.background"]').inputValue();
        expect(path.endsWith(`.${extension}`)).toBeTruthy();
        const response = await context.request.get(base + path);
        expect(response.status()).toBe(200);
        expect(response.headers()['content-type']).toContain(mime);
        if (extension !== 'svg') expect(Buffer.compare(await response.body(), buffer)).toBe(0);
        else expect(await response.text()).toContain('<rect');
        results.push({ extension, path, status: response.status() });
        await page.getByRole('button', { name: 'Cancel', exact: true }).click();
    }
    await settings();
    await page.locator('[id="mountedActionSchema0.restore_defaults"]').click();
    await page.getByRole('button', { name: 'Submit', exact: true }).click();
    await page.getByText('Appearance saved', { exact: true }).waitFor();
    await writeFile('test-results/upload-formats-report.json', JSON.stringify(results, null, 2));
    console.log('PASS: real GIF, WebP and SVG form uploads, persistence and image responses.');
} catch (error) {
    console.log((await page.locator('body').innerText()).slice(-2500));
    await page.screenshot({ path: 'test-results/failure-formats.png', fullPage: true });
    throw error;
} finally { await browser.close(); }
