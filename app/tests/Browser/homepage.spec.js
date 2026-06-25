import { expect, test } from '@playwright/test';

test('Arabic homepage keeps every primary section usable', async ({ page }) => {
    await page.route(/\/build\/assets\/site-.*\.js$/, route => route.abort());
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveTitle(/أمل العيسى/);
    for (const id of ['services', 'work', 'stores', 'social', 'clients', 'logos', 'freebies', 'contact']) {
        await expect(page.locator(`#${id}`)).toBeVisible();
    }
    await page.locator('#loader').evaluate(node => node.remove());
    for (const viewport of [{ name: 'desktop', width: 1440, height: 900 }, { name: 'tablet', width: 1024, height: 1366 }, { name: 'mobile', width: 390, height: 844 }]) {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });
        const overflowRule = await page.evaluate(() => getComputedStyle(document.body).overflowX);
        expect(overflowRule, `${viewport.name} should prevent horizontal scrolling`).toBe('hidden');
    }
});

test('English page has stable localized metadata and no horizontal overflow', async ({ page }) => {
    await page.route(/\/build\/assets\/site-.*\.js$/, route => route.abort());
    await page.goto('/en', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('html')).toHaveAttribute('lang', 'en');
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    expect(overflow).toBeLessThanOrEqual(1);
});
