<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Entities\WantslistItemsEntity;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Wantslists API.
 *
 * WARNING: These tests create and modify wantslists!
 */
class WantslistsTest extends TestCase
{
    private const TEST_PREFIX = 'E2E';

    private ?int $createdWantslistId = null;

    /**
     * Test getting all wantslists.
     */
    public function testGetWantsLists(): void
    {
        $result = $this->client->wantslist()->getWantsLists();
        $this->logResponse('getWantsLists', $result);

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
        $name = 'E2EW' . substr(date('is'), -4);
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $result = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('createWantsList', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('wantslist', $result);
        $this->assertNotEmpty($result['wantslist']);

        $wantslist = $result['wantslist'][0];
        $this->assertArrayHasKey('idWantsList', $wantslist);
        $this->assertEquals($name, $wantslist['name']);

        $this->createdWantslistId = $wantslist['idWantsList'];
        info(sprintf('Created wantslist: %s (ID: %d)', $name, $this->createdWantslistId));
    }

    /**
     * Test getting wantslist details.
     */
    public function testGetWantsList(): void
    {
        // First create a wantslist
        $name = 'E2EG' . substr(date('is'), -4);
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('createWantsList_forGet', $createResult);
        $wantslistId = $createResult['wantslist'][0]['idWantsList'];

        $result = $this->client->wantslist()->getWantsList($wantslistId);
        $this->logResponse('getWantsList', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('wantslist', $result);
        $this->assertEquals($wantslistId, $result['wantslist']['idWantsList']);

        info(sprintf('Wantslist %d retrieved successfully', $wantslistId));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test getting non-existent wantslist fails.
     */
    public function testGetNonExistentWantslistFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->wantslist()->getWantsList(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test renaming a wantslist.
     */
    public function testRenameWantsList(): void
    {
        // First create a wantslist
        $name = 'E2ER' . substr(date('is'), -4);
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('createWantsList', $createResult);
        $wantslistId = $createResult['wantslist'][0]['idWantsList'];

        $newName = 'E2RN' . substr(date('is'), -4);
        $result = $this->client->wantslist()->renameWantsList($wantslistId, $newName);
        $this->logResponse('renameWantsList', $result);

        $this->assertIsArray($result);

        info(sprintf('Renamed wantslist %d to "%s"', $wantslistId, $newName));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test renaming non-existent wantslist fails.
     */
    public function testRenameNonExistentWantslistFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->wantslist()->renameWantsList(999999999, 'New Name'),
            HttpClientException::class,
        );
    }

    /**
     * Test adding items to wantslist.
     */
    public function testAddItemsToWantsList(): void
    {
        // Create a wantslist
        $name = 'E2EI' . substr(date('is'), -4);
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('createWantsList', $createResult);
        $wantslistId = $createResult['wantslist'][0]['idWantsList'];

        // Add item
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $items = new WantslistItemsEntity([
            [
                'idProduct' => $productId,
                'count' => 1,
                'minCondition' => 'NM',
                'idLanguage' => 1,
            ],
        ]);

        $result = $this->client->wantslist()->addItemsToWantsList($wantslistId, $items);
        $this->logResponse('addItemsToWantsList', $result);

        $this->assertIsArray($result);

        info(sprintf('Added item (product %d) to wantslist %d', $productId, $wantslistId));

        // Cleanup
        $this->client->wantslist()->deleteWantsList($wantslistId);
    }

    /**
     * Test adding items to non-existent wantslist fails.
     */
    public function testAddItemsToNonExistentWantslistFails(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $items = new WantslistItemsEntity([
            [
                'idProduct' => $productId,
                'count' => 1,
                'minCondition' => 'NM',
                'idLanguage' => 1,
            ],
        ]);

        $this->assertThrows(
            fn () => $this->client->wantslist()->addItemsToWantsList(999999999, $items),
            HttpClientException::class,
        );
    }

    /**
     * Test deleting a wantslist.
     */
    public function testDeleteWantsList(): void
    {
        // Create a wantslist to delete
        $name = 'E2ED' . substr(date('is'), -4);
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);

        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('createWantsList', $createResult);
        $wantslistId = $createResult['wantslist'][0]['idWantsList'];

        $result = $this->client->wantslist()->deleteWantsList($wantslistId);
        $this->logResponse('deleteWantsList', $result);

        $this->assertIsArray($result);

        info(sprintf('Deleted wantslist %d', $wantslistId));
    }

    /**
     * Test deleting non-existent wantslist fails.
     */
    public function testDeleteNonExistentWantslistFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->wantslist()->deleteWantsList(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test full wantslist lifecycle: create -> add items -> edit items -> delete items -> delete wantslist.
     */
    public function testWantslistLifecycle(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        // Step 1: Create wantslist
        $name = 'E2EL' . substr(date('is'), -4);
        $createResult = $this->client->wantslist()->createWantsList($name, $gameId);
        $this->logResponse('lifecycle_createWantsList', $createResult);
        $wantslistId = $createResult['wantslist'][0]['idWantsList'];
        info(sprintf('Step 1: Created wantslist %d', $wantslistId));

        // Step 2: Add items
        $items = new WantslistItemsEntity([
            [
                'idProduct' => $productId,
                'count' => 2,
                'minCondition' => 'EX',
                'idLanguage' => 1,
                'isFoil' => false,
            ],
        ]);

        $addResult = $this->client->wantslist()->addItemsToWantsList($wantslistId, $items);
        $this->logResponse('lifecycle_addItems', $addResult);
        $this->assertIsArray($addResult);
        info('Step 2: Added items to wantslist');

        // Step 3: Get wantslist with items
        $getResult = $this->client->wantslist()->getWantsList($wantslistId);
        $this->logResponse('lifecycle_getWantsList', $getResult);
        $this->assertArrayHasKey('wantslist', $getResult);

        // Get item ID for editing
        $wantItems = $getResult['wantslist']['item'] ?? [];
        info(sprintf('Step 3: Wantslist has %d items', count($wantItems)));

        if (!empty($wantItems)) {
            $itemId = $wantItems[0]['idWant'];

            // Step 4: Edit item
            $editItems = new WantslistItemsEntity([
                [
                    'idWant' => $itemId,
                    'count' => 4,
                    'minCondition' => 'LP',
                ],
            ]);

            $editResult = $this->client->wantslist()->editItemsInWantsList($wantslistId, $editItems);
            $this->logResponse('lifecycle_editItems', $editResult);
            $this->assertIsArray($editResult);
            info(sprintf('Step 4: Edited item %d (count: 2->4, condition: EX->LP)', $itemId));

            // Step 5: Delete item
            $deleteItems = new WantslistItemsEntity([
                ['idWant' => $itemId],
            ]);

            $deleteItemResult = $this->client->wantslist()->deleteItemsFromWantsList($wantslistId, $deleteItems);
            $this->logResponse('lifecycle_deleteItems', $deleteItemResult);
            $this->assertIsArray($deleteItemResult);
            info(sprintf('Step 5: Deleted item %d from wantslist', $itemId));

            // Step 6: Try to delete same item again - should fail or return empty
            try {
                $this->client->wantslist()->deleteItemsFromWantsList($wantslistId, $deleteItems);
                // If no exception, the API might just ignore non-existent items
                info('Step 6: API accepted double delete (no error)');
            } catch (HttpClientException $e) {
                info('Step 6: Double delete correctly rejected by Cardmarket');
            }
        }

        // Step 7: Delete wantslist
        $deleteResult = $this->client->wantslist()->deleteWantsList($wantslistId);
        $this->logResponse('lifecycle_deleteWantsList', $deleteResult);
        $this->assertIsArray($deleteResult);
        info(sprintf('Step 7: Deleted wantslist %d', $wantslistId));

        // Step 8: Try to delete same wantslist again - should fail
        $this->assertThrows(
            fn () => $this->client->wantslist()->deleteWantsList($wantslistId),
            HttpClientException::class,
        );
        info('Step 8: Double delete correctly rejected by Cardmarket');
    }

    /**
     * Cleanup any E2E test wantslists.
     */
    public function testCleanupTestWantslists(): void
    {
        $result = $this->client->wantslist()->getWantsLists();
        $this->logResponse('getWantsLists_cleanup', $result);

        if (empty($result['wantslist'])) {
            info('No wantslists to cleanup');

            return;
        }

        $deleted = 0;
        foreach ($result['wantslist'] as $wantslist) {
            if (str_starts_with($wantslist['name'], self::TEST_PREFIX)) {
                try {
                    $this->client->wantslist()->deleteWantsList($wantslist['idWantsList']);
                    $deleted++;
                } catch (\Throwable $e) {
                    warning(sprintf('Could not delete wantslist %d: %s', $wantslist['idWantsList'], $e->getMessage()));
                }
            }
        }

        info(sprintf('Cleaned up %d E2E test wantslists', $deleted));
    }
}
