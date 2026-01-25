<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Orders API.
 */
class OrdersTest extends TestCase
{
    /**
     * Test getting received orders.
     */
    public function testGetReceivedOrders(): void
    {
        $result = $this->client->orders()->getReceivedOrders();

        $this->assertIsArray($result);

        if (isset($result['order'])) {
            info(sprintf('Found %d received orders', count($result['order'])));
        } else {
            info('No received orders');
        }
    }

    /**
     * Test getting sent orders (purchases).
     */
    public function testGetSentOrders(): void
    {
        $result = $this->client->orders()->getSentOrders();

        $this->assertIsArray($result);

        if (isset($result['order'])) {
            info(sprintf('Found %d sent orders', count($result['order'])));
        } else {
            info('No sent orders');
        }
    }

    /**
     * Test getting orders with filters.
     */
    public function testGetOrdersWithFilters(): void
    {
        // Get paid orders
        $result = $this->client->orders()->getOrders('buyer', 2); // 2 = paid

        $this->assertIsArray($result);

        $count = isset($result['order']) ? count($result['order']) : 0;
        info(sprintf('Found %d paid orders as buyer', $count));
    }

    /**
     * Test getting specific order details.
     */
    public function testGetOrderDetails(): void
    {
        // First get list of orders
        $ordersResult = $this->client->orders()->getReceivedOrders();

        if (empty($ordersResult['order'])) {
            // Try sent orders
            $ordersResult = $this->client->orders()->getSentOrders();
        }

        if (empty($ordersResult['order'])) {
            $this->skip('No orders to retrieve details for');
        }

        $orderId = $ordersResult['order'][0]['idOrder'];
        $result = $this->client->orders()->getOrder($orderId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('order', $result);

        $order = $result['order'];
        $this->assertEquals($orderId, $order['idOrder']);

        info(sprintf('Order %d details retrieved successfully', $orderId));
    }
}
