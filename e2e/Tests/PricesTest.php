<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

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
}
