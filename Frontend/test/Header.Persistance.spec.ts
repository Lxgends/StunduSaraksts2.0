import { test, expect } from '@playwright/test';

test.describe('Page persistence after root page visit and refresh', () => {
  test('should stay on kurss ipa21 after visiting kurss, going to root page, and refreshing', async ({ page }) => {
    
    await page.goto('http://localhost:5173/kurss?kurss=IPa21');

    await expect(page).toHaveURL('http://localhost:5173/kurss?kurss=IPa21');

    await page.goto('http://localhost:5173/');

    await page.waitForTimeout(1000);

    await page.reload();

    await expect(page).toHaveURL('http://localhost:5173/kurss?kurss=IPa21');
  });

  test('should stay on pasniedzejs Jēkabs Krīgerts after visiting pasniedzejs, going to root page, and refreshing', async ({ page }) => {
    const encodedName = encodeURIComponent('Jēkabs Krīgerts');

    await page.goto(`http://localhost:5173/pasniedzejs?name=${encodedName}`);

    await expect(page).toHaveURL(`http://localhost:5173/pasniedzejs?name=${encodedName}`);

    await page.goto('http://localhost:5173/');

    await page.waitForTimeout(1000);

    await page.reload();

    await expect(page).toHaveURL(`http://localhost:5173/pasniedzejs?name=${encodedName}`);
  });

  test('should stay on kabinets 203 after visiting kabinets, going to root page, and refreshing', async ({ page }) => {

    await page.goto('http://localhost:5173/kabinets?number=203');

    await expect(page).toHaveURL('http://localhost:5173/kabinets?number=203');

    await page.goto('http://localhost:5173/');

    await page.waitForTimeout(1000);

    await page.reload();

    await expect(page).toHaveURL('http://localhost:5173/kabinets?number=203');
  });
});
