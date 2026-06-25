import { defineConfig, devices } from '@playwright/test';
import { existsSync } from 'node:fs';
import { join } from 'node:path';

const systemChrome = process.platform === 'win32'
    ? 'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe'
    : null;
const localChromium = systemChrome && existsSync(systemChrome)
    ? systemChrome
    : (process.env.LOCALAPPDATA ? join(process.env.LOCALAPPDATA, 'ms-playwright', 'chromium-1208', 'chrome-win64', 'chrome.exe') : null);

export default defineConfig({
    testDir: './tests/Browser',
    timeout: 120_000,
    workers: 1,
    expect: { timeout: 30_000 },
    use: {
        baseURL: 'http://127.0.0.1:8134',
        locale: 'ar-SA',
        colorScheme: 'light',
        launchOptions: localChromium && existsSync(localChromium) ? { executablePath: localChromium } : {},
    },
    webServer: {
        command: 'php artisan serve --host=127.0.0.1 --port=8134',
        url: 'http://127.0.0.1:8134',
        reuseExistingServer: false,
        timeout: 60_000,
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
