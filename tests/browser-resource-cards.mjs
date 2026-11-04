import { chromium, expect } from '@playwright/test';
import { readFile, writeFile } from 'node:fs/promises';

const browser = await chromium.launch({ headless: true, ...(process.env.VETRA_CHROME ? { executablePath: process.env.VETRA_CHROME } : {}) });
const context = await browser.newContext({ storageState: 'test-results/session.json', viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
page.setDefaultNavigationTimeout(120_000);
const base = process.env.VETRA_URL || 'http://127.0.0.1:8765';

try {
    const server = (await readFile(process.env.VETRA_SERVER_PATH || '../.tools/server-path.txt', 'utf8')).trim();
    await page.goto(base + server, { waitUntil: 'networkidle' });
    await expect(page.locator('#vetra-theme')).toHaveCount(1);
    expect((await page.locator('#vetra-theme').textContent()).length).toBeGreaterThan(10000);
    const serverSurfaces = await page.evaluate(() => {
        const group = document.querySelector('.fi-wi-stats-overview .fi-section');
        const cards = [...document.querySelectorAll('.fi-wi-stats-overview .fi-small-stat-block')];
        const powerGroup = document.querySelector('.fi-console-page .fi-header-actions-ctn .fi-btn-group');
        const powerButtons = [...powerGroup.querySelectorAll(':scope > .fi-btn')];
        const style = element => ({
            background: getComputedStyle(element).backgroundColor,
            border: getComputedStyle(element).borderTopWidth,
            radius: getComputedStyle(element).borderTopLeftRadius,
            shadow: getComputedStyle(element).boxShadow,
        });
        return {
            overview: { group: style(group), cards: cards.map(style) },
            power: {
                group: style(powerGroup),
                buttons: powerButtons.map(button => ({
                    ...style(button),
                    width: getComputedStyle(button).width,
                    height: getComputedStyle(button).height,
                    fontSize: getComputedStyle(button).fontSize,
                    accessibleName: button.textContent.trim(),
                    svg: button.querySelector('svg:not(.fi-loading-indicator)') !== null,
                })),
            },
        };
    });
    const { overview, power } = serverSurfaces;
    expect(overview.group.background).toBe('rgba(0, 0, 0, 0)');
    expect(overview.group.border).toBe('0px');
    expect(overview.group.shadow).toBe('none');
    expect(overview.cards).toHaveLength(6);
    for (const card of overview.cards) {
        expect(card.background).not.toBe('rgba(0, 0, 0, 0)');
        expect(card.border).toBe('1px');
        expect(card.radius).not.toBe('0px');
        expect(card.shadow).not.toBe('none');
    }
    expect(power.group.background).toBe('rgba(0, 0, 0, 0)');
    expect(power.group.border).toBe('0px');
    expect(power.group.shadow).toBe('none');
    expect(power.buttons).toHaveLength(3);
    for (const button of power.buttons) {
        expect(button.border).toBe('1px');
        expect(button.radius).not.toBe('0px');
        expect(button.background).not.toBe('rgba(0, 0, 0, 0)');
        expect(button.width).toBe(button.height);
        expect(button.fontSize).toBe('0px');
        expect(button.accessibleName.length).toBeGreaterThan(0);
        expect(button.svg).toBe(true);
    }
    await page.screenshot({ path: 'test-results/07-resource-cards.png', fullPage: true });

    await page.goto(base + '/admin/plugins', { waitUntil: 'networkidle' });
    const surfaces = await page.evaluate(() => [...document.querySelectorAll('.fi-page-header-main-ctn > .fi-header, .fi-ta > .fi-ta-ctn')].map(element => ({
        background: getComputedStyle(element).backgroundColor,
        border: getComputedStyle(element).borderTopWidth,
        radius: getComputedStyle(element).borderTopLeftRadius,
        shadow: getComputedStyle(element).boxShadow,
    })));
    expect(surfaces).toHaveLength(2);
    for (const surface of surfaces) {
        expect(surface.background).not.toBe('rgba(0, 0, 0, 0)');
        expect(surface.border).toBe('1px');
        expect(surface.radius).not.toBe('0px');
        expect(surface.shadow).not.toBe('none');
    }
    await page.screenshot({ path: 'test-results/08-admin-surfaces.png', fullPage: true });
    await writeFile('test-results/surface-regression-report.json', JSON.stringify({ overview, power, admin: surfaces }, null, 2));
    console.log('PASS: resource and power groups are frameless; their cards/buttons and other surfaces keep individual frames.');
} finally {
    await browser.close();
}
