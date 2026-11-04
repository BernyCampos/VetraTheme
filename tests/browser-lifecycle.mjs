import { chromium, expect } from '@playwright/test';
const browser = await chromium.launch({ headless: true, ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}) });
const page = await browser.newPage();
try {
    await page.goto((process.env.VETRA_URL || 'http://127.0.0.1:8765') + '/login', { waitUntil: 'networkidle' });
    await expect(page.locator('input[id="form.login"]')).toBeVisible();
    const installed = process.env.VETRA_EXPECT_INSTALLED === 'true';
    await expect(page.locator('#vetra-ambient')).toHaveCount(installed ? 1 : 0);
    await page.screenshot({ path: `test-results/${installed ? 'reinstalled' : 'uninstalled'}-login.png`, fullPage: true });
    console.log(`PASS: ${installed ? 'theme restored after ZIP import' : 'native login restored after uninstall'}`);
} finally { await browser.close(); }
