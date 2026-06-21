import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/Browser',
    timeout: 45_000,
    expect: { timeout: 8_000 },
    use: { baseURL: 'http://127.0.0.1:8134', locale: 'ar-SA', colorScheme: 'light' },
    webServer: {
        command: 'php artisan serve --host=127.0.0.1 --port=8134',
        url: 'http://127.0.0.1:8134',
        reuseExistingServer: false,
        timeout: 30_000,
    },
    projects: [
        { name: 'desktop', use: { ...devices['Desktop Chrome'] } },
        { name: 'tablet', use: { ...devices['iPad Pro 11'] } },
        { name: 'mobile', use: { ...devices['iPhone 13'] } },
    ],
});
