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
        $result = $this->client->stock()->getStockFile($gameId);

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
            'price' => 9999.99, // Extremely high price so no one buys it
            'condition' => 'NM',
            'idLanguage' => 1,
            'comments' => self::TEST_COMMENT,
            'isFoil' => false,
            'isSigned' => false,
            'isAltered' => false,
        ]);

        $result = $resource->send();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('inserted', $result);

        if (!empty($result['inserted']['idArticle'])) {
            $articleId = is_array($result['inserted']['idArticle'])
                ? $result['inserted']['idArticle'][0]
                : $result['inserted']['idArticle'];

            $this->createdArticleId = (int) $articleId;
            info(sprintf('Created article ID: %d with comment "%s"', $this->createdArticleId, self::TEST_COMMENT));
        } elseif (!empty($result['inserted']) && isset($result['inserted'][0]['idArticle'])) {
            $this->createdArticleId = (int) $result['inserted'][0]['idArticle'];
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
        ]);

        $this->assertThrows(
            fn () => $resource->send(),
            HttpClientException::class,
        );
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
        ]);

        $result = $resource->send();

        $this->assertIsArray($result);
        info(sprintf('Updated article %d price: %.2f -> %.2f', $articleId, $originalPrice, $newPrice));

        // Revert the price and comment
        $resource = $this->client->updateArticleStock();
        $resource->add([
            'idArticle' => $articleId,
            'price' => $originalPrice,
            'comments' => $article['comments'] ?? '',
        ]);
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
        ]);

        $this->assertThrows(
            fn () => $resource->send(),
            HttpClientException::class,
        );
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
        $result = $this->client->stock()->findStockArticles('Lightning');

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
        $result = $this->client->stock()->getStockArticlesOfProduct($productId);

        $this->assertIsArray($result);

        $count = count($result['article'] ?? []);
        info(sprintf('Found %d of your articles for product %d', $count, $productId));
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
            'price' => 8888.88,
            'condition' => 'LP',
            'idLanguage' => 1,
            'comments' => self::TEST_COMMENT . ' - Lifecycle Test',
            'isFoil' => false,
            'isSigned' => false,
            'isAltered' => false,
        ]);

        $addResult = $addResource->send();
        $this->assertIsArray($addResult);

        // Extract article ID
        $articleId = null;
        if (!empty($addResult['inserted']['idArticle'])) {
            $articleId = is_array($addResult['inserted']['idArticle'])
                ? (int) $addResult['inserted']['idArticle'][0]
                : (int) $addResult['inserted']['idArticle'];
        } elseif (!empty($addResult['inserted']) && isset($addResult['inserted'][0]['idArticle'])) {
            $articleId = (int) $addResult['inserted'][0]['idArticle'];
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
            'price' => 7777.77,
            'comments' => self::TEST_COMMENT . ' - Lifecycle Updated',
        ]);

        $updateResult = $updateResource->send();
        $this->assertIsArray($updateResult);
        info(sprintf('Step 2: Updated article %d', $articleId));

        // Step 3: Delete article
        $deleteResource = $this->client->deleteArticleStock();
        $deleteResource->add([
            'idArticle' => $articleId,
            'count' => 1,
        ]);

        $deleteResult = $deleteResource->send();
        $this->assertIsArray($deleteResult);
        info(sprintf('Step 3: Deleted article %d', $articleId));

        // Step 4: Try to delete again - should fail
        $deleteResource2 = $this->client->deleteArticleStock();
        $deleteResource2->add([
            'idArticle' => $articleId,
            'count' => 1,
        ]);

        $this->assertThrows(
            fn () => $deleteResource2->send(),
            HttpClientException::class,
        );
        info('Step 4: Double delete correctly rejected by Cardmarket');
    }

    /**
     * Test deleting article from stock.
     *
     * This cleans up E2E test articles.
     */
    public function testDeleteArticleFromStock(): void
    {
        // Get an E2E test article or any article with high price
        $stockResult = $this->client->stock()->getStock();

        if (empty($stockResult['article'])) {
            $this->skip('No articles in stock to delete');
        }

        // Find an article with our E2E test comment
        $articleToDelete = null;
        foreach ($stockResult['article'] as $article) {
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
        ]);

        $result = $resource->send();

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
        ]);

        $this->assertThrows(
            fn () => $resource->send(),
            HttpClientException::class,
        );
    }

    /**
     * Cleanup all remaining E2E test articles.
     */
    public function testCleanupTestArticles(): void
    {
        $stockResult = $this->client->stock()->getStock();

        if (empty($stockResult['article'])) {
            info('No articles in stock to cleanup');

            return;
        }

        $deleted = 0;
        foreach ($stockResult['article'] as $article) {
            if (isset($article['comments']) && str_contains($article['comments'], '[E2E Test Item]')) {
                $resource = $this->client->deleteArticleStock();
                $resource->add([
                    'idArticle' => $article['idArticle'],
                    'count' => $article['count'],
                ]);

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
