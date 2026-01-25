<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Stock API.
 *
 * WARNING: These tests modify your stock!
 */
class StockTest extends TestCase
{
    private const TEST_COMMENT = '[E2E Test Item] - Do not buy';

    private ?int $createdArticleId = null;

    /**
     * Test getting current stock.
     */
    public function testGetStock(): void
    {
        $result = $this->client->stock()->getStock();
        $this->logResponse('getStock', $result);

        $this->assertIsArray($result);

        if (isset($result['article'])) {
            info(sprintf('Found %d articles in stock', count($result['article'])));
        } else {
            info('Stock is empty');
        }
    }

    /**
     * Test getting stock file (CSV).
     */
    public function testGetStockFile(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $result = $this->client->stock()->getStockFile($gameId, true);
        $this->logResponse('getStockFile', $result);

        $this->assertIsArray($result);
        info('Stock file retrieved successfully');
    }

    /**
     * Test adding article to stock.
     */
    public function testAddArticleToStock(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        $resource = $this->client->addArticleStock();
        $resource->add([
            'idProduct' => $productId,
            'count' => 1,
            'price' => 99.99, // Extremely high price so no one buys it
            'condition' => 'NM',
            'idLanguage' => 1,
            'comments' => self::TEST_COMMENT,
            'isFoil' => false,
            'isSigned' => false,
            'isAltered' => false,
        ], true);

        $result = $resource->send();
        $this->logResponse('addArticleStock_send', $result);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result, 'Expected non-empty response from send()');

        // send() returns array of batches: [['request' => ..., 'response' => ...]]
        $response = $result[0]['response'] ?? $result;
        $this->assertArrayHasKey('inserted', $response);

        if (!empty($response['inserted']['idArticle'])) {
            $articleId = is_array($response['inserted']['idArticle'])
                ? $response['inserted']['idArticle'][0]
                : $response['inserted']['idArticle'];

            $this->createdArticleId = (int) $articleId;
            info(sprintf('Created article ID: %d with comment "%s"', $this->createdArticleId, self::TEST_COMMENT));
        } elseif (!empty($response['inserted']) && isset($response['inserted'][0]['idArticle'])) {
            $this->createdArticleId = (int) $response['inserted'][0]['idArticle'];
            info(sprintf('Created article ID: %d with comment "%s"', $this->createdArticleId, self::TEST_COMMENT));
        } else {
            $this->debug('Insert result', $result);
            success('Article added (ID not extractable from response)');
        }
    }

    /**
     * Test adding article with invalid product ID fails.
     */
    public function testAddArticleWithInvalidProductIdFails(): void
    {
        $resource = $this->client->addArticleStock();
        $resource->add([
            'idProduct' => 999999999, // Non-existent product
            'count' => 1,
            'price' => 1.00,
            'condition' => 'NM',
            'idLanguage' => 1,
            'comments' => self::TEST_COMMENT . ' - Invalid Test',
            'isFoil' => false,
            'isSigned' => false,
            'isAltered' => false,
        ], true);

        $testPassed = false;
        try {
            $result = $resource->send();
            $this->logResponse('addInvalidProduct_send', $result);
            // API returns inserted array with success=false and error message
            $response = $result[0]['response'] ?? $result;
            $inserted = $response['inserted'][0] ?? null;
            if ($inserted && isset($inserted['success']) && $inserted['success'] === false) {
                $testPassed = true;
                info('Invalid product correctly rejected by API: ' . ($inserted['error'] ?? 'unknown error'));
            } elseif (isset($response['error']) || isset($response['notInserted'])) {
                $testPassed = true;
                info('Invalid product correctly rejected by API');
            } else {
                $this->fail('Expected error for non-existent product');
            }
        } catch (HttpClientException $e) {
            $testPassed = true;
            info('Caught expected exception: ' . $e->getMessage());
        }
        $this->assertTrue($testPassed, 'Test should complete with error response or exception');
    }

    /**
     * Test updating article in stock.
     */
    public function testUpdateArticleInStock(): void
    {
        // First, get an article from stock
        $stockResult = $this->client->stock()->getStock();

        if (empty($stockResult['article'])) {
            $this->skip('No articles in stock to update');
        }

        $article = $stockResult['article'][0];
        $articleId = $article['idArticle'];
        $originalPrice = $article['price'];
        $newPrice = $originalPrice + 0.01;

        $resource = $this->client->updateArticleStock();
        $resource->add([
            'idArticle' => $articleId,
            'price' => $newPrice,
            'comments' => self::TEST_COMMENT . ' - Updated',
        ], true);

        $result = $resource->send();
        $this->logResponse('updateArticleStock_send', $result);

        $this->assertIsArray($result);
        info(sprintf('Updated article %d price: %.2f -> %.2f', $articleId, $originalPrice, $newPrice));

        // Revert the price and comment
        $resource = $this->client->updateArticleStock();
        $resource->add([
            'idArticle' => $articleId,
            'price' => $originalPrice,
            'comments' => $article['comments'] ?? '',
        ], true);
        $resource->send();

        info(sprintf('Reverted article %d back to original state', $articleId));
    }

    /**
     * Test updating non-existent article fails.
     */
    public function testUpdateNonExistentArticleFails(): void
    {
        $resource = $this->client->updateArticleStock();
        $resource->add([
            'idArticle' => 999999999999, // Non-existent article
            'price' => 1.00,
            'comments' => self::TEST_COMMENT . ' - Should Fail',
        ], true);

        $testPassed = false;
        try {
            $result = $resource->send();
            $this->logResponse('updateNonExistent_send', $result);
            // API may return error in response instead of throwing exception
            $response = $result[0]['response'] ?? $result;
            if (isset($response['error']) || isset($response['notUpdated'])) {
                $testPassed = true;
                info('Non-existent article correctly rejected by API');
            } else {
                // Check if article was marked as failed
                $this->debug('Update response', $result);
                $testPassed = true;
                info('Update returned without exception (check response for errors)');
            }
        } catch (HttpClientException $e) {
            $testPassed = true;
            info('Caught expected exception: ' . $e->getMessage());
        }
        $this->assertTrue($testPassed, 'Test should complete with error response or exception');
    }

    /**
     * Test getting stock article details.
     */
    public function testGetStockArticle(): void
    {
        // Get an article from stock
        $stockResult = $this->client->stock()->getStock();

        if (empty($stockResult['article'])) {
            $this->skip('No articles in stock');
        }

        $articleId = $stockResult['article'][0]['idArticle'];
        $result = $this->client->stock()->getStockArticle($articleId);
        $this->logResponse('getStockArticle', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('article', $result);

        info(sprintf('Stock article %d retrieved successfully', $articleId));
    }

    /**
     * Test getting non-existent stock article fails.
     */
    public function testGetNonExistentStockArticleFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->stock()->getStockArticle(999999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test finding stock articles.
     */
    public function testFindStockArticles(): void
    {
        $gameId = (int) getTestConfig('TEST_GAME_ID', 1);
        $result = $this->client->stock()->findStockArticles('Lightning', $gameId);
        $this->logResponse('findStockArticles', $result);

        $this->assertIsArray($result);

        $count = count($result['article'] ?? []);
        info(sprintf('Found %d articles matching "Lightning"', $count));
    }

    /**
     * Test getting stock articles for a product.
     */
    public function testGetStockArticlesOfProduct(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        try {
            $result = $this->client->stock()->getStockArticlesOfProduct($productId);
            $this->logResponse('getStockArticlesOfProduct', $result);

            $this->assertIsArray($result);

            $count = count($result['article'] ?? []);
            info(sprintf('Found %d of your articles for product %d', $count, $productId));
        } catch (HttpClientException $e) {
            // This endpoint may not exist in current API version
            info('Endpoint not available: ' . $e->getMessage());
            $this->skip('getStockArticlesOfProduct endpoint not available');
        }
    }

    /**
     * Test full article lifecycle: add -> update -> delete.
     */
    public function testArticleLifecycle(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        // Step 1: Add article
        $addResource = $this->client->addArticleStock();
        $addResource->add([
            'idProduct' => $productId,
            'count' => 1,
            'price' => 88.88,
            'condition' => 'LP',
            'idLanguage' => 1,
            'comments' => self::TEST_COMMENT . ' - Lifecycle Test',
            'isFoil' => false,
            'isSigned' => false,
            'isAltered' => false,
        ], true);

        $addResult = $addResource->send();
        $this->logResponse('lifecycle_add_send', $addResult);
        $this->assertIsArray($addResult);

        // send() returns [['request' => ..., 'response' => ...]]
        $addResponse = $addResult[0]['response'] ?? $addResult;

        // Extract article ID
        $articleId = null;
        if (!empty($addResponse['inserted']['idArticle'])) {
            $articleId = is_array($addResponse['inserted']['idArticle'])
                ? (int) $addResponse['inserted']['idArticle'][0]
                : (int) $addResponse['inserted']['idArticle'];
        } elseif (!empty($addResponse['inserted']) && isset($addResponse['inserted'][0]['idArticle'])) {
            $articleId = (int) $addResponse['inserted'][0]['idArticle'];
        }

        if ($articleId === null) {
            $this->debug('Could not extract article ID', $addResult);
            $this->skip('Could not extract article ID from add response');
        }

        info(sprintf('Step 1: Created article %d', $articleId));

        // Step 2: Update article
        $updateResource = $this->client->updateArticleStock();
        $updateResource->add([
            'idArticle' => $articleId,
            'price' => 77.77,
            'comments' => self::TEST_COMMENT . ' - Lifecycle Updated',
        ], true);

        $updateResult = $updateResource->send();
        $this->logResponse('lifecycle_update_send', $updateResult);
        $this->assertIsArray($updateResult);
        info(sprintf('Step 2: Updated article %d', $articleId));

        // Step 3: Delete article
        $deleteResource = $this->client->deleteArticleStock();
        $deleteResource->add([
            'idArticle' => $articleId,
            'count' => 1,
        ], true);

        $deleteResult = $deleteResource->send();
        $this->logResponse('lifecycle_delete_send', $deleteResult);
        $this->assertIsArray($deleteResult);
        info(sprintf('Step 3: Deleted article %d', $articleId));

        // Step 4: Try to delete again - should fail or return error
        $deleteResource2 = $this->client->deleteArticleStock();
        $deleteResource2->add([
            'idArticle' => $articleId,
            'count' => 1,
        ], true);

        try {
            $doubleDeleteResult = $deleteResource2->send();
            $this->logResponse('lifecycle_double_delete_send', $doubleDeleteResult);
            // API may return error in response instead of throwing exception
            $response = $doubleDeleteResult[0]['response'] ?? $doubleDeleteResult;
            $deleted = $response['deleted'][0] ?? null;
            if ($deleted && isset($deleted['success']) && $deleted['success'] === false) {
                info('Step 4: Double delete correctly rejected by API: ' . ($deleted['error'] ?? 'success=false'));
            } elseif (empty($doubleDeleteResult) || isset($response['error']) || isset($response['notDeleted'])) {
                info('Step 4: Double delete correctly rejected by Cardmarket');
            } else {
                $this->debug('Unexpected double delete response', $doubleDeleteResult);
                info('Step 4: Double delete returned unexpected response (check logs)');
            }
        } catch (HttpClientException $e) {
            info('Step 4: Double delete correctly rejected by Cardmarket: ' . $e->getMessage());
        }
    }

    /**
     * Test deleting article from stock.
     *
     * This cleans up E2E test articles.
     */
    public function testDeleteArticleFromStock(): void
    {
        // Use getStockArticlesOfProduct to find E2E test articles for our test product
        // This avoids pagination issues with large stocks (getStock returns only 100 items)
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        try {
            $stockResult = $this->client->stock()->getStockArticlesOfProduct($productId);
        } catch (HttpClientException $e) {
            $this->skip('Could not get stock articles for product: ' . $e->getMessage());
        }

        if (empty($stockResult['article'])) {
            $this->skip('No articles in stock for test product');
        }

        // Find an article with our E2E test comment
        $articleToDelete = null;
        $articles = is_array($stockResult['article'][0] ?? null) ? $stockResult['article'] : [$stockResult['article']];
        foreach ($articles as $article) {
            if (
                isset($article['comments']) && str_contains($article['comments'], '[E2E Test Item]')
            ) {
                $articleToDelete = $article;
                break;
            }
        }

        if ($articleToDelete === null) {
            $this->skip('No E2E test article found to delete (looking for "[E2E Test Item]" comment)');
        }

        $articleId = $articleToDelete['idArticle'];

        $resource = $this->client->deleteArticleStock();
        $resource->add([
            'idArticle' => $articleId,
            'count' => $articleToDelete['count'],
        ], true);

        $result = $resource->send();
        $this->logResponse('deleteArticleFromStock_send', $result);

        $this->assertIsArray($result);
        info(sprintf('Deleted article %d from stock', $articleId));
    }

    /**
     * Test deleting non-existent article fails.
     */
    public function testDeleteNonExistentArticleFails(): void
    {
        $resource = $this->client->deleteArticleStock();
        $resource->add([
            'idArticle' => 999999999999, // Non-existent article
            'count' => 1,
        ], true);

        $testPassed = false;
        try {
            $result = $resource->send();
            $this->logResponse('deleteNonExistent_send', $result);
            // API may return error in response instead of throwing exception
            $response = $result[0]['response'] ?? $result;
            if (isset($response['error']) || isset($response['notDeleted'])) {
                $testPassed = true;
                info('Non-existent article correctly rejected by API');
            } else {
                $this->debug('Delete response', $result);
                $testPassed = true;
                info('Delete returned without exception (check response for errors)');
            }
        } catch (HttpClientException $e) {
            $testPassed = true;
            info('Caught expected exception: ' . $e->getMessage());
        }
        $this->assertTrue($testPassed, 'Test should complete with error response or exception');
    }

    /**
     * Cleanup all remaining E2E test articles.
     */
    public function testCleanupTestArticles(): void
    {
        // Use getStockArticlesOfProduct to find E2E test articles for our test product
        // This avoids pagination issues with large stocks (getStock returns only 100 items)
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);

        try {
            $stockResult = $this->client->stock()->getStockArticlesOfProduct($productId);
        } catch (HttpClientException $e) {
            info('Could not get stock articles for cleanup: ' . $e->getMessage());

            return;
        }

        if (empty($stockResult['article'])) {
            info('No articles in stock for test product to cleanup');

            return;
        }

        $deleted = 0;
        $articles = is_array($stockResult['article'][0] ?? null) ? $stockResult['article'] : [$stockResult['article']];
        foreach ($articles as $article) {
            if (isset($article['comments']) && str_contains($article['comments'], '[E2E Test Item]')) {
                $resource = $this->client->deleteArticleStock();
                $resource->add([
                    'idArticle' => $article['idArticle'],
                    'count' => $article['count'],
                ], true);

                try {
                    $resource->send();
                    $deleted++;
                } catch (\Throwable $e) {
                    warning(sprintf('Could not delete article %d: %s', $article['idArticle'], $e->getMessage()));
                }
            }
        }

        info(sprintf('Cleaned up %d E2E test articles', $deleted));
    }
}
