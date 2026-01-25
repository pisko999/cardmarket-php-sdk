<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

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
     * Test getting non-existent product fails.
     */
    public function testGetNonExistentProductFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->products()->getProductDetails(999999999),
            HttpClientException::class,
        );
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
     * Test finding products with no results.
     */
    public function testFindProductsNoResults(): void
    {
        $searchData = [
            'search' => 'XyzNonExistent12345CardName67890',
            'idGame' => 1,
            'idLanguage' => 1,
        ];

        $result = $this->client->products()->findProducts($searchData);

        $this->assertIsArray($result);
        // Should return empty result, not error
        $count = count($result['product'] ?? []);
        $this->assertEquals(0, $count, 'Expected 0 products for non-existent search');

        info('No products found for non-existent search term (expected)');
    }

    /**
     * Test finding products with invalid game ID.
     */
    public function testFindProductsWithInvalidGameId(): void
    {
        $searchData = [
            'search' => 'Lightning Bolt',
            'idGame' => 99999, // Non-existent game
            'idLanguage' => 1,
        ];

        // This might either fail or return empty results depending on API
        try {
            $result = $this->client->products()->findProducts($searchData);
            $this->assertIsArray($result);
            info('Invalid game ID returned empty results (API allows it)');
        } catch (HttpClientException $e) {
            info('Invalid game ID correctly rejected by API');
        }
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

    /**
     * Test getting image for non-existent product fails.
     */
    public function testGetImageForNonExistentProductFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->products()->getProductImage(999999999),
            HttpClientException::class,
        );
    }
}
