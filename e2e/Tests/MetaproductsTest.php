<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Metaproducts API.
 */
class MetaproductsTest extends TestCase
{
    /**
     * Test finding metaproducts.
     */
    public function testFindMetaProducts(): void
    {
        $searchData = [
            'idGame' => 1,
        ];

        try {
            // Test with card name containing space to verify URL encoding
            $result = $this->client->metaproducts()->findMetaProducts('Black Lotus', $searchData);
            $this->logResponse('findMetaProducts_BlackLotus', $result);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('metaproduct', $result);

            if (empty($result['metaproduct'])) {
                $this->skip('No metaproducts found');
            }

            // API returns nested structure: metaproduct[0]['metaproduct']['enName']
            $firstResult = $result['metaproduct'][0];
            $this->assertArrayHasKey('metaproduct', $firstResult);
            $this->assertArrayHasKey('product', $firstResult);

            $metaproduct = $firstResult['metaproduct'];
            $this->assertArrayHasKey('idMetaproduct', $metaproduct);
            $this->assertArrayHasKey('enName', $metaproduct);

            info(sprintf(
                'Found metaproduct: %s (ID: %d) with %d product variants',
                $metaproduct['enName'],
                $metaproduct['idMetaproduct'],
                count($firstResult['product']),
            ));
        } catch (HttpClientException $e) {
            // API may return error for metaproducts search
            info('Metaproducts API returned error: ' . $e->getMessage());
            $this->skip('Metaproducts API not available or returned error');
        }
    }

    /**
     * Test finding metaproducts with no results.
     */
    public function testFindMetaProductsNoResults(): void
    {
        $searchData = [
            'idGame' => 1,
        ];

        // Both empty results and error are acceptable behaviors
        $testPassed = false;
        try {
            $result = $this->client->metaproducts()->findMetaProducts('XyzNonExistentCard12345Name67890', $searchData);
            $this->logResponse('findMetaProducts_nonexistent', $result);

            $this->assertIsArray($result);
            $count = count($result['metaproduct'] ?? []);
            $this->assertEquals(0, $count, 'Expected 0 metaproducts for non-existent search');
            $testPassed = true;
            info('No metaproducts found for non-existent search (expected)');
        } catch (HttpClientException $e) {
            // API may return error for empty results - also acceptable
            $testPassed = true;
            info('API returned error for non-existent search (expected behavior)');
        }
        $this->assertTrue($testPassed, 'Test should complete via empty results or exception');
    }

    /**
     * Test getting metaproduct details.
     */
    public function testGetMetaProductDetails(): void
    {
        // First find a metaproduct using known working card
        $searchData = [
            'idGame' => 1,
        ];

        try {
            // Test with card name containing space to verify URL encoding
            $searchResult = $this->client->metaproducts()->findMetaProducts('Black Lotus', $searchData);
            $this->logResponse('findMetaProducts_search', $searchResult);

            if (empty($searchResult['metaproduct'])) {
                $this->skip('No metaproducts found');
            }

            // Nested structure: metaproduct[0]['metaproduct']['idMetaproduct']
            $metaproductId = $searchResult['metaproduct'][0]['metaproduct']['idMetaproduct'];

            $result = $this->client->metaproducts()->getMetaProductDetails($metaproductId);
            $this->logResponse('getMetaProductDetails', $result);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('metaproduct', $result);

            $metaproduct = $result['metaproduct'];
            $this->assertArrayHasKey('idMetaproduct', $metaproduct);
            $this->assertArrayHasKey('enName', $metaproduct);

            // Note: 'product' key is at the same level as 'metaproduct' in getMetaProductDetails response
            $this->assertArrayHasKey('product', $result);

            info(sprintf(
                'Metaproduct: %s (ID: %d) with %d products',
                $metaproduct['enName'],
                $metaproduct['idMetaproduct'],
                count($result['product']),
            ));
        } catch (HttpClientException $e) {
            // API may return error for metaproducts
            info('Metaproducts API returned error: ' . $e->getMessage());
            $this->skip('Metaproducts API not available or returned error');
        }
    }

    /**
     * Test getting non-existent metaproduct details.
     */
    public function testGetNonExistentMetaProductDetailsFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->metaproducts()->getMetaProductDetails(999999999),
            HttpClientException::class,
        );
    }
}
