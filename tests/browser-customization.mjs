// Real settings upload, Markdown, palette persistence and layout regression test.
import { chromium, expect } from '@playwright/test';
import { readFile, writeFile } from 'node:fs/promises';
const browser = await chromium.launch({ headless: true, ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}) });
const context = await browser.newContext({ storageState: 'test-results/session.json', viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));
const base = process.env.VETRA_URL || 'http://127.0.0.1:8765';
const field = key => page.locator(`[id="mountedActionSchema0.${key}"]`);
const markdown = '**Aviso global**\n\n- *Mantenimiento programado*\n- [Estado del servicio](https://example.com/status)\n\nConsulta los detalles antes de continuar.';
const palette = { dark_page: '#142023', dark_surface: '#21343c', dark_sidebar: '#24313a', dark_topbar: '#293442', dark_text: '#f4eadf', dark_muted: '#bdcbd9', dark_border: '#89b7c7', dark_notice: '#24372f', dark_notice_accent: '#91c4a4', dark_input: '#20363d', dark_console: '#17242b', light_page: '#e8f1eb', light_surface: '#fffaf1', light_sidebar: '#f0f6f2', light_topbar: '#e2ece6', light_text: '#25352f', light_muted: '#486758', light_border: '#5b7d69', light_notice: '#eaf3ed', light_notice_accent: '#3b7056', light_input: '#fcfff9', light_console: '#18242b' };
async function settings() {
    await page.goto(base + '/admin/plugins', { waitUntil: 'networkidle' });
    await page.getByRole('button', { name: 'Ajustes', exact: true }).click();
    await page.getByText('Apariencia de VetraTheme', { exact: true }).waitFor();
}
async function save() {
    await page.getByRole('button', { name: 'Enviar', exact: true }).click();
    await page.getByText('Apariencia guardada', { exact: true }).waitFor({ timeout: 30000 });
}
try {
    await settings();
    await field('notice').fill(markdown);
    await page.getByText('Colores de todos los elementos del tema', { exact: true }).click();
    for (const [key, value] of Object.entries(palette)) await field(key).fill(value);
    await page.locator('input[type=file]').setInputFiles('tests/fixtures/background.png');
    await expect(page.locator('.filepond--item[data-filepond-item-state="processing-complete"]')).toHaveCount(1, { timeout: 20000 });
    await save();
    await settings();
    await expect(field('notice')).toHaveValue(markdown);
    await page.getByText('Colores de todos los elementos del tema', { exact: true }).click();
    for (const [key, value] of Object.entries(palette)) await expect(field(key)).toHaveValue(value);
    const background = await field('background').inputValue();
    expect(background).toMatch(/^\/vetra-theme\/background\/[a-f0-9]{40}\.png$/);
    const imageResponse = await context.request.get(base + background);
    expect(imageResponse.status()).toBe(200);
    expect(imageResponse.headers()['content-type']).toContain('image/png');
    expect(imageResponse.headers()['x-content-type-options']).toBe('nosniff');
    expect(Buffer.compare(await imageResponse.body(), await readFile('tests/fixtures/background.png'))).toBe(0);
    await expect(page.locator('img[alt="Fondo actual"]')).toBeVisible();
    await page.getByRole('button', { name: 'Cancelar', exact: true }).click();
    const server = (await readFile(process.env.VETRA_SERVER_PATH || '../.tools/server-path.txt', 'utf8')).trim();
    const layouts = [];
    for (const [panel, url] of [['admin', '/admin/plugins'], ['app', '/'], ['server', server]]) {
        await page.goto(base + url, { waitUntil: 'networkidle' });
        await expect(page.locator('.vetra-notice strong')).toHaveText('Aviso global');
        await expect(page.locator('.vetra-notice li')).toHaveCount(2);
        for (const mode of ['dark', 'light']) {
            await page.evaluate(mode => window.Alpine.store('theme', mode), mode);
            for (const width of [1440, 390]) {
                await page.setViewportSize({ width, height: width === 390 ? 844 : 1000 });
                await page.waitForTimeout(250);
                const metrics = await page.evaluate(() => {
                    const notice = document.querySelector('.vetra-notice').getBoundingClientRect();
                    const bar = document.querySelector('.fi-topbar').getBoundingClientRect();
                    const section = document.querySelector('.fi-wi-stats-overview .fi-section');
                    const statCard = document.querySelector('.fi-wi-stats-overview .fi-small-stat-block');
                    const header = document.querySelector('.fi-page-header-main-ctn > .fi-header');
                    const table = document.querySelector('.fi-ta > .fi-ta-ctn');
                    const surface = element => element ? {
                        background: getComputedStyle(element).backgroundColor,
                        border: getComputedStyle(element).borderTopWidth,
                        radius: getComputedStyle(element).borderTopLeftRadius,
                        shadow: getComputedStyle(element).boxShadow,
                    } : null;
                    return { gap: Math.round(notice.top - bar.bottom), overflow: document.documentElement.scrollWidth > innerWidth + 2, page: getComputedStyle(document.body).backgroundColor, header: surface(header), table: surface(table), statCard: surface(statCard), groupBackground: section ? getComputedStyle(section).backgroundColor : null, groupShadow: section ? getComputedStyle(section).boxShadow : null, cards: document.querySelectorAll('.fi-small-stat-block').length };
                });
                expect(metrics.gap).toBeGreaterThanOrEqual(20);
                expect(metrics.overflow).toBe(false);
                expect(metrics.page).toBe(mode === 'dark' ? 'rgb(20, 32, 35)' : 'rgb(232, 241, 235)');
                expect(metrics.header).not.toBeNull();
                expect(metrics.header.background).not.toBe('rgba(0, 0, 0, 0)');
                expect(metrics.header.border).toBe('1px');
                expect(metrics.header.radius).not.toBe('0px');
                expect(metrics.header.shadow).not.toBe('none');
                if (metrics.table) {
                    expect(metrics.table.background).not.toBe('rgba(0, 0, 0, 0)');
                    expect(metrics.table.border).toBe('1px');
                    expect(metrics.table.radius).not.toBe('0px');
                    expect(metrics.table.shadow).not.toBe('none');
                }
                if (panel === 'server') {
                    expect(metrics.groupBackground).toBe('rgba(0, 0, 0, 0)');
                    expect(metrics.groupShadow).toBe('none');
                    expect(metrics.cards).toBe(6);
                    expect(metrics.statCard.background).not.toBe('rgba(0, 0, 0, 0)');
                    expect(metrics.statCard.border).toBe('1px');
                    expect(metrics.statCard.radius).not.toBe('0px');
                    expect(metrics.statCard.shadow).not.toBe('none');
                }
                layouts.push({ panel, mode, width, ...metrics });
                await page.screenshot({ path: `test-results/custom-${panel}-${mode}-${width}.png`, fullPage: true });
            }
        }
        await page.setViewportSize({ width: 1440, height: 1000 });
    }
    // Remove/reset through the same real form; pending image is not deleted by reset.
    await settings();
    await field('remove_background').click();
    await save();
    await settings();
    await expect(field('background')).toHaveValue('');
    await field('restore_defaults').click();
    await save();
    await settings();
    await expect(field('notice')).toHaveValue('');
    await page.getByText('Colores de todos los elementos del tema', { exact: true }).click();
    await expect(field('dark_page')).toHaveValue('#111927');
    await expect(field('light_page')).toHaveValue('#e8edf5');
    await writeFile('test-results/customization-report.json', JSON.stringify({ background, layouts, errors, upload: true, markdown: true, palettePersistence: true, restored: true }, null, 2));
    expect(errors).toEqual([]);
    console.log('PASS: real upload, Markdown, 22 saved palette colors, spacing and transparent overview in 12 layouts; restore verified.');
} catch (error) {
    console.log((await page.locator('body').innerText()).slice(-3000));
    await page.screenshot({ path: 'test-results/failure-customization.png', fullPage: true });
    throw error;
} finally { await browser.close(); }
