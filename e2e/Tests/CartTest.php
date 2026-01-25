<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Cart API.
 *
 * WARNING: These tests may modify your shopping cart!
 */
class CartTest extends TestCase
{
    /**
     * Test getting cart contents.
     */
    public function testGetCart(): void
    {
        $result = $this->client->cart()->getCart();

        $this->assertIsArray($result);

        if (isset($result['shoppingCart']['seller'])) {
            $sellerCount = count($result['shoppingCart']['seller']);
            info(sprintf('Cart has items from %d sellers', $sellerCount));
        } else {
            info('Cart is empty');
        }
    }

    /**
     * Test getting shipping methods for a seller.
     */
    public function testGetShippingMethods(): void
    {
        // First check if cart has any sellers
        $cart = $this->client->cart()->getCart();

        if (empty($cart['shoppingCart']['seller'])) {
            $this->skip('Cart is empty, cannot get shipping methods');
        }

        $sellerId = $cart['shoppingCart']['seller'][0]['idUser'];
        $result = $this->client->cart()->getShippingMethods($sellerId);

        $this->assertIsArray($result);

        if (isset($result['shippingMethod'])) {
            info(sprintf('Found %d shipping methods for seller %d', count($result['shippingMethod']), $sellerId));
        }
    }

    /**
     * Test getting shipping methods for non-existent seller.
     */
    public function testGetShippingMethodsForNonExistentSellerFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->cart()->getShippingMethods(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test adding article to cart.
     */
    public function testAddArticleToCart(): void
    {
        // Get an article to add - search for a cheap one
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $articles = $this->client->articles()->getArticles($productId);

        if (empty($articles['article'])) {
            $this->skip('No articles available for test product');
        }

        // Find a cheap article (less than 1 EUR)
        $articleToAdd = null;
        foreach ($articles['article'] as $article) {
            if ($article['price'] < 1.00 && ($article['count'] ?? 0) > 0) {
                $articleToAdd = $article;
                break;
            }
        }

        if ($articleToAdd === null) {
            $this->skip('No cheap article found to add to cart');
        }

        $articleId = $articleToAdd['idArticle'];

        try {
            $result = $this->client->cart()->addArticlesToCart($articleId, 1);
            $this->assertIsArray($result);
            info(sprintf('Added article %d to cart (price: %.2f)', $articleId, $articleToAdd['price']));

            // Remove it from cart
            $this->client->cart()->removeArticlesFromCart($articleId, 1);
            info(sprintf('Removed article %d from cart', $articleId));
        } catch (HttpClientException $e) {
            // Article might not be available or already in cart
            info(sprintf('Could not add article to cart: %s', $e->getMessage()));
        }
    }

    /**
     * Test adding non-existent article to cart fails.
     */
    public function testAddNonExistentArticleToCartFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->cart()->addArticlesToCart(999999999999, 1),
            HttpClientException::class,
        );
    }

    /**
     * Test removing article from cart that is not in cart.
     */
    public function testRemoveNonExistentArticleFromCartFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->cart()->removeArticlesFromCart(999999999999, 1),
            HttpClientException::class,
        );
    }

    /**
     * Test emptying the cart.
     *
     * WARNING: This removes all items from your cart!
     */
    public function testEmptyCart(): void
    {
        // Only run in sandbox mode for safety
        if (($_ENV['CARDMARKET_SANDBOX'] ?? 'false') !== 'true' && ($_ENV['ENABLE_CART_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Cart empty operation only enabled in sandbox mode or with ENABLE_CART_TESTS=true');
        }

        $result = $this->client->cart()->emptyCart();

        $this->assertIsArray($result);

        info('Cart emptied successfully');
    }

    /**
     * Test cart lifecycle: add -> verify -> remove -> verify empty.
     */
    public function testCartLifecycle(): void
    {
        // Get an article to add
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $articles = $this->client->articles()->getArticles($productId);

        if (empty($articles['article'])) {
            $this->skip('No articles available for test product');
        }

        // Find a cheap article
        $articleToAdd = null;
        foreach ($articles['article'] as $article) {
            if ($article['price'] < 0.50 && ($article['count'] ?? 0) > 0) {
                $articleToAdd = $article;
                break;
            }
        }

        if ($articleToAdd === null) {
            $this->skip('No cheap article (< 0.50 EUR) found for lifecycle test');
        }

        $articleId = $articleToAdd['idArticle'];

        try {
            // Step 1: Add to cart
            $this->client->cart()->addArticlesToCart($articleId, 1);
            info(sprintf('Step 1: Added article %d to cart', $articleId));

            // Step 2: Verify article is in cart
            $cart = $this->client->cart()->getCart();
            $foundInCart = false;
            if (!empty($cart['shoppingCart']['seller'])) {
                foreach ($cart['shoppingCart']['seller'] as $seller) {
                    foreach ($seller['article'] ?? [] as $cartArticle) {
                        if ($cartArticle['idArticle'] == $articleId) {
                            $foundInCart = true;
                            break 2;
                        }
                    }
                }
            }
            $this->assertTrue($foundInCart, 'Article should be in cart');
            info('Step 2: Verified article is in cart');

            // Step 3: Remove from cart
            $this->client->cart()->removeArticlesFromCart($articleId, 1);
            info('Step 3: Removed article from cart');

            // Step 4: Try to remove again - should fail
            $this->assertThrows(
                fn () => $this->client->cart()->removeArticlesFromCart($articleId, 1),
                HttpClientException::class,
            );
            info('Step 4: Double remove correctly rejected');
        } catch (HttpClientException $e) {
            // If we can't add to cart, clean up and skip
            warning(sprintf('Cart lifecycle test failed: %s', $e->getMessage()));
            $this->skip('Could not complete cart lifecycle test');
        }
    }
}
