const { test, expect } = require('@playwright/test');

test.describe('Admin Creation Tests', () => {
    test('should create an admin successfully', async ({ page }) => {
        await page.goto('http://localhost:5173/admin');

        await page.fill('input[type="email"]', 'ipb21.m.vimba@vtdt.edu.lv');
        await page.fill('input[type="password"]', 'sigmagigmaligma');
        await page.click('button[type="submit"]');

        await page.waitForNavigation({ waitUntil: 'networkidle' });

        await page.goto('http://localhost:5173/admin/administratoraizveide');

        await page.fill('input[name="Epasts"]', 'newadmin@example.com');
        await page.fill('input[name="Parole"]', 'newpassword123');
        await page.fill('input[name="Parole_confirmation"]', 'newpassword123');

        await page.click('button[type="submit"]');

        page.on('response', response => {
            console.log(`Response received: ${response.url()} - Status: ${response.status()}`);
        });

        const response = await page.waitForResponse(response => 
            response.url().includes('/api/administrators') && 
            response.status() === 200,
            { timeout: 10000 }
        );

        const responseBody = await response.json();
        console.log(responseBody);

        expect(responseBody.status).toBe('success'); 
        expect(responseBody.data.admin.Epasts).toBe('newadmin@example.com'); 
    });

    test('should show error message on invalid login', async ({ page }) => {
        await page.goto('http://localhost:5173/admin');

        await page.fill('input[type="email"]', 'invalid@example.com');
        await page.fill('input[type="password"]', 'wrongpassword');
        await page.click('button[type="submit"]');

        await expect(page.locator('.error-message')).toBeVisible();
    });
});
