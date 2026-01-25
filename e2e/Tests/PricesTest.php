<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Prices API.
 */
class PricesTest extends TestCase
{
    /**
     * Test getting price guide for a product.
     */
    public function testGetProductPriceGuide(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $result = $this->client->prices()->getProductPriceGuide($productId);

        // Returns base64 encoded data or false
        if ($result === false) {
            $this->skip('No price guide available for product');
        }

        $this->assertNotEmpty($result);
        info('Price guide retrieved successfully');
    }

    /**
     * Test getting price guide for non-existent product.
     */
    public function testGetPriceGuideForNonExistentProductFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->prices()->getProductPriceGuide(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test getting price guide for multiple products.
     */
    public function testGetPriceGuideForMultipleProducts(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        // Get price guide for same product twice - should work both times
        $result1 = $this->client->prices()->getProductPriceGuide($productId);
        $result2 = $this->client->prices()->getProductPriceGuide($productId);

        if ($result1 === false && $result2 === false) {
            $this->skip('No price guide available for product');
        }

        // Both should return same result
        $this->assertEquals($result1, $result2, 'Price guide should be consistent');

        info('Multiple price guide requests successful and consistent');
    }
}
