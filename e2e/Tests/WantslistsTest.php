<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Wantslists API.
 *
 * WARNING: These tests create and modify wantslists!
 */
class WantslistsTest extends TestCase
{
    private ?int $createdWantslistId = null;

    /**
     * Test getting all wantslists.
     */
    public function testGetWantsLists(): void
    {
        $result = $this->client->wantslist()->getWantsLists();

        $this->assertIsArray($result);

        if (isset($result['wantslist'])) {
            info(sprintf('Found %d wantslists', count($result['wantslist'])));
        } else {
            info('No wantslists found');
        }
    }

    /**
     * Test creating a wantslist.
     */
    public function testCreateWantsList(): void
    {
        $name = 'E2E Test Wantslist ' . date('Y-m-d H:i:s');
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $result = $this->client->wantslist()->createWantsList($name, $gameId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('wantslist', $result);

        $wantslist = $result['wantslist'];
        $this->assertArrayHasKey('idWantslist', $wantslist);
        $this->assertEquals($name, $wantslist['name']);

        $this->createdWantslistId = $wantslist['idWantslist'];
        info(sprintf('Created wantslist: %s (ID: %d)', $name, $this->createdWantslistId));
    }

    /**
     * Test getting wantslist details.
     */
    public function testGetWantsList(): void
    {
        // First create a wantslist
        $name = 'E2E Test Get ' . date('Y-m-d H:i:s');
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $wantslistId = $createResult['wantslist']['idWantslist'];

        $result = $this->client->wantslist()->getWantsList($wantslistId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('wantslist', $result);
        $this->assertEquals($wantslistId, $result['wantslist']['idWantslist']);

        info(sprintf('Wantslist %d retrieved successfully', $wantslistId));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test renaming a wantslist.
     */
    public function testRenameWantsList(): void
    {
        // First create a wantslist
        $name = 'E2E Test Rename ' . date('Y-m-d H:i:s');
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $wantslistId = $createResult['wantslist']['idWantslist'];

        $newName = 'E2E Renamed ' . date('Y-m-d H:i:s');
        $result = $this->client->wantslist()->renameWantsList($wantslistId, $newName);

        $this->assertIsArray($result);

        info(sprintf('Renamed wantslist %d to "%s"', $wantslistId, $newName));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test adding items to wantslist.
     */
    public function testAddItemsToWantsList(): void
    {
        // Create a wantslist
        $name = 'E2E Test Items ' . date('Y-m-d H:i:s');
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $wantslistId = $createResult['wantslist']['idWantslist'];

        // Add item
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $items = new \Pisko\CardMarket\Entities\WantslistItemsEntity([
            [
                'idProduct' => $productId,
                'count' => 1,
                'minCondition' => 'NM',
                'idLanguage' => 1,
            ],
        ]);

        $result = $this->client->wantslist()->addItemsToWantsList($wantslistId, $items);

        $this->assertIsArray($result);

        info(sprintf('Added item (product %d) to wantslist %d', $productId, $wantslistId));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test deleting a wantslist.
     */
    public function testDeleteWantsList(): void
    {
        // Create a wantslist to delete
        $name = 'E2E Test Delete ' . date('Y-m-d H:i:s');
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $wantslistId = $createResult['wantslist']['idWantslist'];

        $result = $this->client->wantslist()->deleteWantsList($wantslistId);

        $this->assertIsArray($result);

        info(sprintf('Deleted wantslist %d', $wantslistId));
    }

    /**
     * Cleanup any E2E test wantslists.
     */
    public function testCleanupTestWantslists(): void
    {
        $result = $this->client->wantslist()->getWantsLists();

        if (empty($result['wantslist'])) {
            info('No wantslists to cleanup');

            return;
        }

        $deleted = 0;
        foreach ($result['wantslist'] as $wantslist) {
            if (str_starts_with($wantslist['name'], 'E2E')) {
                $this->client->wantslist()->deleteWantsList($wantslist['idWantslist']);
                $deleted++;
            }
        }

        info(sprintf('Cleaned up %d E2E test wantslists', $deleted));
    }
}
