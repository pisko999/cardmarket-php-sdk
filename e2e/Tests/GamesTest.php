<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Games API.
 */
class GamesTest extends TestCase
{
    /**
     * Test getting list of all games.
     */
    public function testGetGamesList(): void
    {
        $result = $this->client->games()->getGamesList();
        $this->logResponse('getGamesList', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('game', $result);
        $this->assertNotEmpty($result['game']);

        info(sprintf('Found %d games', count($result['game'])));

        // Verify MTG is in the list
        $mtgFound = false;
        foreach ($result['game'] as $game) {
            if ($game['idGame'] === 1) {
                $mtgFound = true;
                $this->assertEquals('Magic the Gathering', $game['name']);
                break;
            }
        }

        $this->assertTrue($mtgFound, 'Magic: The Gathering should be in games list');
    }

    /**
     * Test games list is consistent on multiple calls.
     */
    public function testGamesListConsistency(): void
    {
        $result1 = $this->client->games()->getGamesList();
        $this->logResponse('getGamesList_1', $result1);
        $result2 = $this->client->games()->getGamesList();
        $this->logResponse('getGamesList_2', $result2);

        $this->assertEquals(
            count($result1['game']),
            count($result2['game']),
            'Games list should be consistent between calls',
        );

        info('Games list consistency verified');
    }
}
