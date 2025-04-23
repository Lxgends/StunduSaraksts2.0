import { test, expect } from '@playwright/test';

test.describe('Header dropdowns', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://localhost:5173');
  });

  test('should link to kurss ipa21', async ({ page }) => {
    await page.getByRole('button', { name: 'Kurss' }).click();
    const kurssLink = page.getByRole('link', { name: 'IPa21' });
    await expect(kurssLink).toHaveAttribute('href', '/kurss?kurss=IPa21');
  });

  test('should link to pasniedzējs Jēkabs Krīgerts', async ({ page }) => {
    await page.getByRole('button', { name: 'Pasniedzējs' }).click();
    const pasniedzLink = page.getByRole('link', { name: /J\. Krīgerts/i });
    await expect(pasniedzLink).toHaveAttribute(
      'href',
      `/pasniedzejs?name=${encodeURIComponent('Jēkabs Krīgerts')}`
    );
  });

  test('should link to kabinets 203', async ({ page }) => {
    await page.getByRole('button', { name: 'Kabinets' }).click();
    const kabLink = page.getByRole('link', { name: /C\. 203/i });
    await expect(kabLink).toHaveAttribute('href', '/kabinets?number=203');
  });
});
