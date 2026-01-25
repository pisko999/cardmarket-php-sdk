<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Expansions API.
 */
class ExpansionsTest extends TestCase
{
    /**
     * Test getting expansions list for a game.
     */
    public function testGetExpansionsListByGame(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $result = $this->client->expansions()->getExpansionsListByGame($gameId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('expansion', $result);
        $this->assertNotEmpty($result['expansion']);

        info(sprintf('Found %d expansions for game %d', count($result['expansion']), $gameId));

        // Verify structure
        $expansion = $result['expansion'][0];
        $this->assertArrayHasKey('idExpansion', $expansion);
        $this->assertArrayHasKey('enName', $expansion);
    }

    /**
     * Test getting cards list for an expansion.
     */
    public function testGetCardsListByExpansion(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $expansionId = (int) getTestConfig('TEST_EXPANSION_ID', 1525);

        $result = $this->client->expansions()->getCardsListByExpansion($gameId, $expansionId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('single', $result);
        $this->assertNotEmpty($result['single']);

        info(sprintf('Found %d cards in expansion %d', count($result['single']), $expansionId));

        // Verify structure
        $card = $result['single'][0];
        $this->assertArrayHasKey('idProduct', $card);
        $this->assertArrayHasKey('enName', $card);
    }
}
