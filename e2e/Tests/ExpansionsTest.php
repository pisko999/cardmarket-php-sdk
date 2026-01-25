<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

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
     * Test getting expansions for non-existent game.
     */
    public function testGetExpansionsForNonExistentGameFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->expansions()->getExpansionsListByGame(99999),
            HttpClientException::class,
        );
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

    /**
     * Test getting cards for non-existent expansion.
     */
    public function testGetCardsForNonExistentExpansionFails(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $this->assertThrows(
            fn () => $this->client->expansions()->getCardsListByExpansion($gameId, 999999),
            HttpClientException::class,
        );
    }

    /**
     * Test expansion details.
     */
    public function testGetExpansionDetails(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $expansionId = (int) getTestConfig('TEST_EXPANSION_ID', 1525);

        $result = $this->client->expansions()->getExpansionDetails($gameId, $expansionId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('expansion', $result);

        $expansion = $result['expansion'];
        $this->assertArrayHasKey('idExpansion', $expansion);
        $this->assertEquals($expansionId, $expansion['idExpansion']);

        info(sprintf('Expansion: %s (ID: %d)', $expansion['enName'], $expansion['idExpansion']));
    }

    /**
     * Test getting details for non-existent expansion.
     */
    public function testGetNonExistentExpansionDetailsFails(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $this->assertThrows(
            fn () => $this->client->expansions()->getExpansionDetails($gameId, 999999),
            HttpClientException::class,
        );
    }
}
