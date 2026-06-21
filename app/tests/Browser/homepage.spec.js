import { expect, test } from '@playwright/test';

test('Arabic homepage keeps every primary section usable', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/أمل العيسى/);
    for (const id of ['services', 'work', 'stores', 'social', 'clients', 'logos', 'freebies', 'contact']) {
        await expect(page.locator(`#${id}`)).toBeVisible();
    }
    await expect(page.locator('body')).toHaveScreenshot('homepage-ar.png', { fullPage: true, animations: 'disabled', maxDiffPixelRatio: 0.02 });
});

test('English page has stable localized metadata and no horizontal overflow', async ({ page }) => {
    await page.goto('/en');
    await expect(page.locator('html')).toHaveAttribute('lang', 'en');
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    expect(overflow).toBeLessThanOrEqual(1);
});
