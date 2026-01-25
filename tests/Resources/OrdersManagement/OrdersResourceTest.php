<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\OrdersManagement;

use Pisko\CardMarket\Entities\EvaluationEntity;
use Pisko\CardMarket\Resources\OrdersManagement\OrdersResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class OrdersResourceTest extends ResourceTestCase
{
    public function testRetrieveSentOrdersForTheCurrentSeller(): void
    {
        $mockResponse = $this->createMockResponse('sent', 20);
        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->getSentOrders();

        $this->assertArrayHasKey('order', $response);
        $this->assertCount(2, $response['order']);
        $this->assertEquals(OrdersResource::ORDER_STATE_SENT, $response['order'][0]['state']['state']);
        $this->assertEquals(OrdersResource::ORDER_STATE_SENT, $response['order'][1]['state']['state']);
    }

    public function testRetrieveReceivedOrdersForTheCurrentSeller(): void
    {
        $mockResponse = $this->createMockResponse('received', 20);
        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->getReceivedOrders();

        $this->assertArrayHasKey('order', $response);
        $this->assertCount(2, $response['order']);
        $this->assertEquals('evaluated', $response['order'][0]['state']['state']);
        $this->assertEquals('evaluated', $response['order'][1]['state']['state']);
    }

    public function testGetOrderById(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'order' => [
                'idOrder' => 12345678,
                'state' => ['state' => 'paid'],
                'seller' => ['idUser' => 1, 'username' => 'TestSeller'],
                'buyer' => ['idUser' => 2, 'username' => 'TestBuyer'],
                'article' => [
                    ['idArticle' => 100, 'count' => 1, 'price' => 10.50],
                ],
                'articleCount' => 1,
                'articleValue' => 10.50,
                'shippingMethod' => ['name' => 'Standard'],
                'totalValue' => 12.50,
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->getOrder(12345678);

        $this->assertArrayHasKey('order', $response);
        $this->assertEquals(12345678, $response['order']['idOrder']);
        $this->assertEquals('paid', $response['order']['state']['state']);
        $this->assertArrayHasKey('seller', $response['order']);
        $this->assertArrayHasKey('buyer', $response['order']);
        $this->assertArrayHasKey('article', $response['order']);
    }

    public function testGetOrdersWithFilters(): void
    {
        $mockResponse = $this->createMockResponse('sent', 25);
        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->getOrders(OrdersResource::ORDER_SELLER, OrdersResource::ORDER_STATE_SENT, 1);

        $this->assertArrayHasKey('order', $response);
        $this->assertIsArray($response['order']);
    }

    public function testGetOrdersWithInvalidActorThrowsException(): void
    {
        $this->setupHttpClientCreatorMock([]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid actor "invalid"');

        $ordersResource->getOrders('invalid', OrdersResource::ORDER_STATE_SENT);
    }

    public function testGetOrdersWithInvalidStateThrowsException(): void
    {
        $this->setupHttpClientCreatorMock([]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid state "invalid"');

        $ordersResource->getOrders(OrdersResource::ORDER_SELLER, 'invalid');
    }

    public function testChangeOrderState(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'order' => [
                'idOrder' => 12345678,
                'state' => ['state' => 'sent'],
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->changeOrderState(12345678, 'send');

        $this->assertArrayHasKey('order', $response);
        $this->assertEquals('sent', $response['order']['state']['state']);
    }

    public function testChangeOrderStateWithReason(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'order' => [
                'idOrder' => 12345678,
                'state' => ['state' => 'cancelled'],
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->changeOrderState(12345678, 'cancel', 'Item not available', 'true');

        $this->assertArrayHasKey('order', $response);
        $this->assertEquals('cancelled', $response['order']['state']['state']);
    }

    public function testSetOrderTrackingNumber(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'order' => [
                'idOrder' => 12345678,
                'state' => ['state' => 'sent'],
                'trackingNumber' => 'DHL123456789',
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->setOrderTrackingNumber(12345678, 'DHL123456789');

        $this->assertArrayHasKey('order', $response);
        $this->assertEquals('DHL123456789', $response['order']['trackingNumber']);
    }

    public function testEvaluateOrder(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'evaluation' => [
                'evaluationGrade' => 1,
                'itemDescription' => 1,
                'packaging' => 1,
                'comment' => 'Great seller, fast shipping!',
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->evaluateOrder(
            12345678,
            1, // Very Good
            1,
            1,
            'Great seller, fast shipping!',
        );

        $this->assertArrayHasKey('evaluation', $response);
        $this->assertEquals(1, $response['evaluation']['evaluationGrade']);
        $this->assertEquals('Great seller, fast shipping!', $response['evaluation']['comment']);
    }

    public function testEvaluateOrderWithComplaints(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'evaluation' => [
                'evaluationGrade' => 4,
                'itemDescription' => 4,
                'packaging' => 3,
                'comment' => 'Card condition was worse than described',
                'complaint' => true,
            ],
        ]), ['response_headers' => ['X-Request-Limit-Max' => 5000, 'X-Request-Limit-Count' => 1]]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $ordersResource = new OrdersResource($this->httpClientCreatorMock);

        $response = $ordersResource->evaluateOrder(
            12345678,
            4, // Bad
            4,
            3,
            'Card condition was worse than described',
            [EvaluationEntity::COMPLAINT_WRONG_GRADING],
        );

        $this->assertArrayHasKey('evaluation', $response);
        $this->assertEquals(4, $response['evaluation']['evaluationGrade']);
    }

    private function createMockResponse(string $state, int $nbUsed): MockResponse
    {
        return new MockResponse(
            file_get_contents(sprintf(__DIR__ . '/../MockResponse/%s_orders.json', $state)),
            [
            'response_headers' => [
              'X-Request-Limit-Max' => 5000,
              'X-Request-Limit-Count' => $nbUsed,
            ],
          ],
        );
    }

    protected function getMockResponses(): array
    {
        return [];
    }
}
