<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Metaproducts API.
 */
class MetaproductsTest extends TestCase
{
    private ?int $metaproductId = null;

    /**
     * Test finding metaproducts.
     */
    public function testFindMetaProducts(): void
    {
        $searchData = [
            'search' => 'Black Lotus',
            'idGame' => 1,
        ];

        $result = $this->client->metaproducts()->findMetaProducts($searchData);

        $this->assertIsArray($result);

        if (empty($result['metaproduct'])) {
            $this->skip('No metaproducts found');
        }

        info(sprintf('Found %d metaproducts', count($result['metaproduct'])));

        // Save for next test
        $this->metaproductId = $result['metaproduct'][0]['idMetaproduct'];
    }

    /**
     * Test getting metaproduct details.
     */
    public function testGetMetaProductDetails(): void
    {
        // First find a metaproduct
        $searchData = [
            'search' => 'Black Lotus',
            'idGame' => 1,
        ];

        $searchResult = $this->client->metaproducts()->findMetaProducts($searchData);

        if (empty($searchResult['metaproduct'])) {
            $this->skip('No metaproducts found');
        }

        $metaproductId = $searchResult['metaproduct'][0]['idMetaproduct'];

        $result = $this->client->metaproducts()->getMetaProductDetails($metaproductId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('metaproduct', $result);

        $metaproduct = $result['metaproduct'];
        $this->assertArrayHasKey('idMetaproduct', $metaproduct);
        $this->assertArrayHasKey('enName', $metaproduct);
        $this->assertArrayHasKey('product', $metaproduct);

        info(sprintf(
            'Metaproduct: %s (ID: %d) with %d products',
            $metaproduct['enName'],
            $metaproduct['idMetaproduct'],
            count($metaproduct['product']),
        ));
    }
}
