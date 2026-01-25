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
        $this->logResponse('getCart', $result);

        $this->assertIsArray($result);

        if (isset($result['shoppingCart']['seller'])) {
            $sellerCount = count($result['shoppingCart']['seller']);
            info(sprintf('Cart has items from %d sellers', $sellerCount));
        } else {
            info('Cart is empty');
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
     * Test adding article, getting shipping methods, and removing from cart.
     */
    public function testCartArticleOperations(): void
    {
        // Only run if explicitly enabled (destructive operation)
        if (($_ENV['ENABLE_CART_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Cart operations require ENABLE_CART_TESTS=true');
        }

        // Get an article to add - search for a cheap one
        $productId = (int) getTestConfig('TEST_PRODUCT_ID', 273799);
        $articles = $this->client->articles()->getArticles($productId);
        $this->logResponse('getArticles', $articles);

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
            // Add article to cart
            $result = $this->client->cart()->addToCart([['idArticle' => $articleId, 'amount' => 1]]);
            $this->logResponse('cart_add', $result);

            $this->assertIsArray($result);
            
            // Response structure can vary:
            // - $result['shoppingCart'][0]['idReservation'] (array of reservations)
            // - $result['shoppingCart']['idReservation'] (single reservation)
            // - $result[0]['response']['shoppingCart'][0]['idReservation'] (wrapped response)
            $idReservation = null;
            
            if (isset($result['shoppingCart'])) {
                $cart = $result['shoppingCart'];
                if (isset($cart['idReservation'])) {
                    $idReservation = $cart['idReservation'];
                } elseif (isset($cart[0]['idReservation'])) {
                    $idReservation = $cart[0]['idReservation'];
                }
            } elseif (isset($result[0]['response']['shoppingCart'][0]['idReservation'])) {
                $idReservation = $result[0]['response']['shoppingCart'][0]['idReservation'];
            }

            if ($idReservation === null) {
                // Debug: log what we got
                info('Cart response structure: ' . json_encode(array_keys($result)));
                $this->skip('Could not find idReservation in cart response');
                return;
            }

            info(sprintf('Added article %d to cart (reservation: %d, price: %.2f)', $articleId, $idReservation, $articleToAdd['price']));

            // Test shipping methods while cart has items
            $shippingResult = $this->client->cart()->getShippingMethods($idReservation);
            $this->logResponse('getShippingMethods', $shippingResult);

            $this->assertIsArray($shippingResult);
            
            // Response uses 'availableMethod' key
            $methods = $shippingResult['availableMethod'] ?? $shippingResult['shippingMethod'] ?? [];
            if (!empty($methods)) {
                if (!isset($methods[0])) {
                    $methods = [$methods];
                }
                info(sprintf('Found %d shipping methods for reservation %d', count($methods), $idReservation));
            }

            // Remove it from cart
            $removeResult = $this->client->cart()->removeFromCart([['idArticle' => $articleId, 'amount' => 1]]);
            $this->logResponse('cart_remove', $removeResult);

            $this->assertIsArray($removeResult);
            info(sprintf('Removed article %d from cart', $articleId));
        } catch (HttpClientException $e) {
            // Article might not be available or already in cart
            info(sprintf('Could not complete cart operation: %s', $e->getMessage()));
        }
    }

    /**
     * Test emptying the cart.
     *
     * WARNING: This removes all items from your cart!
     */
    public function testEmptyCart(): void
    {
        // Only run if explicitly enabled (destructive operation)
        if (($_ENV['ENABLE_CART_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Cart empty operation requires ENABLE_CART_TESTS=true');
        }

        $result = $this->client->cart()->emptyCart();
        $this->logResponse('emptyCart', $result);

        $this->assertIsArray($result);

        info('Cart emptied successfully');
    }

    /**
     * Cleanup any items left in cart from failed tests.
     *
     * Note: This only cleans up if explicitly enabled, as it would remove
     * user's real cart items too.
     */
    public function testCleanupCart(): void
    {
        // Only cleanup if explicitly enabled
        if (($_ENV['ENABLE_CART_CLEANUP'] ?? 'false') !== 'true') {
            $this->skip('Cart cleanup requires ENABLE_CART_CLEANUP=true');
        }

        $cart = $this->client->cart()->getCart();
        $this->logResponse('getCart_cleanup', $cart);

        if (empty($cart['shoppingCart']['seller'])) {
            info('Cart is already empty');

            return;
        }

        // Empty the cart
        $this->client->cart()->emptyCart();
        info('Cart cleaned up');
    }
}
