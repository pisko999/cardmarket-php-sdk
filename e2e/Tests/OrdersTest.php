<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

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
     * Test getting orders with all valid states.
     */
    public function testGetOrdersWithAllStates(): void
    {
        // Test all valid states: 1=bought, 2=paid, 4=sent, 8=received, 32=lost, 64=cancelled
        $states = [1, 2, 4, 8, 32, 64];

        foreach ($states as $state) {
            $result = $this->client->orders()->getOrders('seller', $state);
            $this->assertIsArray($result);
        }

        info('All order states tested successfully');
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

    /**
     * Test getting non-existent order fails.
     */
    public function testGetNonExistentOrderFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->orders()->getOrder(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test changing state of non-existent order fails.
     */
    public function testChangeStateOfNonExistentOrderFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->orders()->changeOrderState(999999999, 'send'),
            HttpClientException::class,
        );
    }

    /**
     * Test adding tracking to non-existent order fails.
     */
    public function testAddTrackingToNonExistentOrderFails(): void
    {
        $trackingEntity = new \Pisko\CardMarket\Entities\TrackingNumberEntity([
            'trackingNumber' => 'TEST123456',
        ]);

        $this->assertThrows(
            fn () => $this->client->orders()->setTrackingNumber(999999999, $trackingEntity),
            HttpClientException::class,
        );
    }

    /**
     * Test adding evaluation to non-existent order fails.
     */
    public function testAddEvaluationToNonExistentOrderFails(): void
    {
        $evaluationEntity = new \Pisko\CardMarket\Entities\EvaluationEntity([
            'evaluationGrade' => 2, // Good
            'comment' => '[E2E Test] This should fail',
            'complaint' => [],
        ]);

        $this->assertThrows(
            fn () => $this->client->orders()->setEvaluation(999999999, $evaluationEntity),
            HttpClientException::class,
        );
    }
}
