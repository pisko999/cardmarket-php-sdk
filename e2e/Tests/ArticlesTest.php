<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Articles API.
 */
class ArticlesTest extends TestCase
{
    /**
     * Test getting articles for a product.
     */
    public function testGetArticles(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $result = $this->client->articles()->getArticles($productId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('article', $result);

        if (empty($result['article'])) {
            $this->skip('No articles found for product');
        }

        info(sprintf('Found %d articles for product %d', count($result['article']), $productId));

        // Verify structure
        $article = $result['article'][0];
        $this->assertArrayHasKey('idArticle', $article);
        $this->assertArrayHasKey('price', $article);
        $this->assertArrayHasKey('condition', $article);
    }

    /**
     * Test getting articles with filters.
     */
    public function testGetArticlesWithFilters(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $searchData = [
            'minCondition' => 'EX',
            'isFoil' => 'false',
            'maxResults' => 10,
        ];

        $result = $this->client->articles()->getArticles($productId, $searchData);

        $this->assertIsArray($result);
        info(sprintf('Found %d filtered articles', count($result['article'] ?? [])));
    }

    /**
     * Test getting articles by user.
     */
    public function testGetArticlesByUser(): void
    {
        $userId = getTestConfig('TEST_OTHER_USER_ID');

        if (empty($userId)) {
            $this->skip('TEST_OTHER_USER_ID not configured');
        }

        $result = $this->client->articles()->getArticlesByUser((int) $userId);

        $this->assertIsArray($result);
        info(sprintf('Found %d articles for user %d', count($result['article'] ?? []), $userId));
    }
}
