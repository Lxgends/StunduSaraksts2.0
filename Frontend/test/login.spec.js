const { test, expect } = require('@playwright/test');

test.describe('Admin Login Tests', () => {
    test('should log in successfully with valid credentials and redirect to the dashboard', async ({ page }) => {
        await page.goto('http://localhost:5173/admin');

        await page.fill('input[type="email"]', 'ipb21.m.vimba@vtdt.edu.lv');
        await page.fill('input[type="password"]', 'sigmagigmaligma');

        await Promise.all([
            page.click('button[type="submit"]'),
            page.waitForNavigation({ waitUntil: 'networkidle' })
        ]);

        expect(page.url()).toBe('http://localhost:5173/admin/dashboard');

        await expect(page.locator('text=dashboard')).toBeVisible();
    });

    test('should show error message on invalid login', async ({ page }) => {
        await page.goto('http://localhost:5173/admin');

        await page.fill('input[type="email"]', 'invalid@example.com');
        await page.fill('input[type="password"]', 'wrongpassword');
        await page.click('button[type="submit"]');

        await expect(page.locator('.error-message')).toBeVisible();
    });
});
