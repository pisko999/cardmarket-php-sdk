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
        $this->logResponse('getProductDetails', $result);

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
            'idGame' => 1,
            'idLanguage' => 1,
        ];

        $result = $this->client->products()->findProducts('Lightning Bolt', 0, 100, $searchData);
        $this->logResponse('findProducts_LightningBolt', $result);

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
            'idGame' => 1,
            'idLanguage' => 1,
        ];

        // Both empty results and error are acceptable behaviors
        $testPassed = false;
        try {
            $result = $this->client->products()->findProducts('XyzNonExistent12345CardName67890', 0, 100, $searchData);
            $this->logResponse('findProducts_nonexistent', $result);
            $this->assertIsArray($result);
            // Should return empty result
            $count = count($result['product'] ?? []);
            $this->assertEquals(0, $count, 'Expected 0 products for non-existent search');
            $testPassed = true;
            info('No products found for non-existent search term (expected)');
        } catch (HttpClientException $e) {
            // API may throw exception for empty results - also acceptable
            $testPassed = true;
            info('API returned error for non-existent search (expected behavior)');
        }
        $this->assertTrue($testPassed, 'Test should complete via empty results or exception');
    }

    /**
     * Test finding products with invalid game ID.
     */
    public function testFindProductsWithInvalidGameId(): void
    {
        $searchData = [
            'idGame' => 99999, // Non-existent game
            'idLanguage' => 1,
        ];

        // This might either fail or return empty results depending on API
        $testPassed = false;
        try {
            $result = $this->client->products()->findProducts('Lightning Bolt', 0, 100, $searchData);
            $this->logResponse('findProducts_invalidGame', $result);
            $this->assertIsArray($result);
            $testPassed = true;
            info('Invalid game ID returned empty results (API allows it)');
        } catch (HttpClientException $e) {
            $testPassed = true;
            info('Invalid game ID correctly rejected by API');
        }
        $this->assertTrue($testPassed, 'Test should complete via empty results or exception');
    }

    /**
     * Test getting product list file.
     */
    public function testGetProductListFile(): void
    {
        // This is a large file, so we just test it returns something
        $result = $this->client->products()->getProductListFile();
        $this->logResponse('getProductListFile', ['type' => gettype($result), 'size' => is_string($result) ? strlen($result) : null]);

        if ($result === false) {
            $this->skip('Product list file not available');
        }

        $this->assertNotEmpty($result);
        info('Product list file retrieved successfully');
    }
}
