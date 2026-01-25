<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Stock API.
 *
 * WARNING: These tests modify your stock!
 */
class StockTest extends TestCase
{
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
            'price' => 999.99, // High price so no one buys it
            'condition' => 'NM',
            'idLanguage' => 1,
            'comments' => 'E2E Test - will be deleted',
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
            info(sprintf('Created article ID: %d', $this->createdArticleId));
        } else {
            // Try to get from different structure
            if (!empty($result['inserted']) && isset($result['inserted'][0]['idArticle'])) {
                $this->createdArticleId = (int) $result['inserted'][0]['idArticle'];
                info(sprintf('Created article ID: %d', $this->createdArticleId));
            } else {
                $this->debug('Insert result', $result);
                success('Article added (ID not extractable from response)');
            }
        }
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
        ]);

        $result = $resource->send();

        $this->assertIsArray($result);
        info(sprintf('Updated article %d price: %.2f -> %.2f', $articleId, $originalPrice, $newPrice));

        // Revert the price
        $resource = $this->client->updateArticleStock();
        $resource->add([
            'idArticle' => $articleId,
            'price' => $originalPrice,
        ]);
        $resource->send();

        info(sprintf('Reverted article %d price back to %.2f', $articleId, $originalPrice));
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
     * Test deleting article from stock.
     *
     * This should run last.
     */
    public function testDeleteArticleFromStock(): void
    {
        // Get an E2E test article or any article with high price
        $stockResult = $this->client->stock()->getStock();

        if (empty($stockResult['article'])) {
            $this->skip('No articles in stock to delete');
        }

        // Find an article with our E2E test comment or very high price
        $articleToDelete = null;
        foreach ($stockResult['article'] as $article) {
            if (
                (isset($article['comments']) && str_contains($article['comments'], 'E2E Test'))
                || $article['price'] >= 999
            ) {
                $articleToDelete = $article;
                break;
            }
        }

        if ($articleToDelete === null) {
            $this->skip('No E2E test article found to delete');
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
}
