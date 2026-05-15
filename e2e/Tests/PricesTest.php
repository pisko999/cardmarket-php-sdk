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
     * Test getting price guide file.
     */
    public function testGetPriceGuideFile(): void
    {
        $result = $this->client->prices()->getPriceGuideFile();
        $this->logResponse('getPriceGuideFile', ['type' => gettype($result), 'size' => is_string($result) ? strlen($result) : null]);

        // Returns CSV content or false
        if ($result === false) {
            $this->skip('No price guide file available');
        }

        $this->assertNotEmpty($result);

        // Check that it looks like CSV data
        $lines = explode("\n", (string) $result);
        $this->assertGreaterThan(1, count($lines), 'Price guide should have multiple lines');

        info(sprintf('Price guide file retrieved (%d lines)', count($lines)));
    }

    /**
     * Test getting price guide file for a specific game.
     */
    public function testGetPriceGuideFileWithGameId(): void
    {
        // Yu-Gi-Oh! (idGame=3)
        $result = $this->client->prices()->getPriceGuideFile(3);
        $this->logResponse('getPriceGuideFile_game3', ['type' => gettype($result), 'size' => is_string($result) ? strlen($result) : null]);

        if ($result === false) {
            $this->skip('No price guide file available for game 3');
        }

        $this->assertNotEmpty($result);

        $lines = explode("\n", (string) $result);
        $this->assertGreaterThan(1, count($lines), 'Price guide should have multiple lines');

        info(sprintf('Price guide file for game 3 retrieved (%d lines)', count($lines)));
    }

    /**
     * Test price guide file consistency.
     */
    public function testPriceGuideFileConsistency(): void
    {
        // Get price guide twice - should return same result
        $result1 = $this->client->prices()->getPriceGuideFile();
        $this->logResponse('getPriceGuideFile_1', ['type' => gettype($result1), 'size' => is_string($result1) ? strlen($result1) : null]);
        $result2 = $this->client->prices()->getPriceGuideFile();
        $this->logResponse('getPriceGuideFile_2', ['type' => gettype($result2), 'size' => is_string($result2) ? strlen($result2) : null]);

        if ($result1 === false && $result2 === false) {
            $this->skip('No price guide file available');
        }

        // Both should return same result
        $this->assertEquals($result1, $result2, 'Price guide should be consistent');

        info('Price guide file requests are consistent');
    }
}
