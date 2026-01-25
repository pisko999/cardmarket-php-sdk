<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Cart API.
 *
 * WARNING: These tests modify your shopping cart!
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
     * Test emptying the cart.
     *
     * WARNING: This removes all items from your cart!
     */
    public function testEmptyCart(): void
    {
        // Only run in sandbox mode for safety
        if (($_ENV['CARDMARKET_SANDBOX'] ?? 'false') !== 'true') {
            $this->skip('Cart operations only enabled in sandbox mode');
        }

        $result = $this->client->cart()->emptyCart();

        $this->assertIsArray($result);

        info('Cart emptied successfully');
    }
}
