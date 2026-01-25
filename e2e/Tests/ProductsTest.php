<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Products API.
 */
class ProductsTest extends TestCase
{
    /**
     * Test getting product details.
     */
    public function testGetProductDetails(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $result = $this->client->products()->getProductDetails($productId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('product', $result);

        $product = $result['product'];
        $this->assertArrayHasKey('idProduct', $product);
        $this->assertArrayHasKey('enName', $product);
        $this->assertEquals($productId, $product['idProduct']);

        info(sprintf('Product: %s (ID: %d)', $product['enName'], $product['idProduct']));
    }

    /**
     * Test finding products by name.
     */
    public function testFindProducts(): void
    {
        $searchData = [
            'search' => 'Lightning Bolt',
            'idGame' => 1,
            'idLanguage' => 1,
        ];

        $result = $this->client->products()->findProducts($searchData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('product', $result);
        $this->assertNotEmpty($result['product']);

        info(sprintf('Found %d products matching "Lightning Bolt"', count($result['product'])));
    }

    /**
     * Test getting product image.
     */
    public function testGetProductImage(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $result = $this->client->products()->getProductImage($productId);

        // Returns base64 encoded image or false
        if ($result === false) {
            $this->skip('Product has no image');
        }

        $this->assertNotEmpty($result);
        info('Product image retrieved successfully');
    }
}
