<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

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
        $this->logResponse('getArticles', $result);

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
     * Test getting articles for non-existent product.
     */
    public function testGetArticlesForNonExistentProductFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->articles()->getArticles(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test getting articles with filters.
     */
    public function testGetArticlesWithFilters(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $searchData = [
            'minCondition' => 'EX',
            'isFoil' => false,
        ];

        $result = $this->client->articles()->getArticles($productId, 0, 10, $searchData);
        $this->logResponse('getArticles_filtered', $result);

        $this->assertIsArray($result);
        info(sprintf('Found %d filtered articles', count($result['article'] ?? [])));
    }

    /**
     * Test getting articles with strict filters that yield no results.
     */
    public function testGetArticlesWithStrictFilters(): void
    {
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $searchData = [
            'minCondition' => 'NM', // Mint only
            'isFoil' => true,
            'isSigned' => false,
            'isAltered' => false, // Very unlikely combination
        ];

        $result = $this->client->articles()->getArticles($productId, 0, 10, $searchData);
        $this->logResponse('getArticles_strict', $result);

        $this->assertIsArray($result);
        $count = count($result['article'] ?? []);
        info(sprintf('Found %d articles with strict filters (expected 0 or few)', $count));
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
        $this->logResponse('getArticlesByUser', $result);

        $this->assertIsArray($result);
        info(sprintf('Found %d articles for user %s', count($result['article'] ?? []), $userId));
    }

    /**
     * Test getting articles by non-existent user.
     */
    public function testGetArticlesByNonExistentUserFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->articles()->getArticlesByUser(999999999),
            HttpClientException::class,
        );
    }
}
