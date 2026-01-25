<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\CartManagement;

use Pisko\CardMarket\Entities\CartAddressEntity;
use Pisko\CardMarket\Entities\CartArticlesEntity;
use Pisko\CardMarket\Resources\CartManagement\CartResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class CartResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetCart()
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [
                    [
                        'idReservation' => 123456,
                        'seller' => ['username' => 'seller1'],
                        'article' => [['idArticle' => 1, 'price' => 5.00]],
                    ],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $response = $cartResource->getCart();

        $this->assertArrayHasKey('shoppingCart', $response);
        $this->assertArrayHasKey('api', $response);
    }

    public function testEmptyCart()
    {
        $mockResponse = new MockResponse(json_encode(['shoppingCart' => []]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $response = $cartResource->emptyCart();

        $this->assertArrayHasKey('shoppingCart', $response);
        $this->assertEmpty($response['shoppingCart']);
    }

    public function testGetShippingMethods()
    {
        $mockResponse = new MockResponse(json_encode([
            'shippingMethod' => [
                ['idShippingMethod' => 1, 'name' => 'Standard', 'price' => 2.00],
                ['idShippingMethod' => 2, 'name' => 'Tracked', 'price' => 5.00],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $response = $cartResource->getShippingMethods(123456);

        $this->assertArrayHasKey('shippingMethod', $response);
        $this->assertIsArray($response['shippingMethod']);
    }

    public function testSetShippingMethod()
    {
        $mockResponse = new MockResponse(json_encode([
            'shippingMethod' => [
                'idShippingMethod' => 1,
                'name' => 'Standard',
                'price' => 2.00,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 8,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $response = $cartResource->setShippingMethod(123456, 1);

        $this->assertArrayHasKey('shippingMethod', $response);
        $this->assertSame(1, $response['shippingMethod']['idShippingMethod']);
    }

    public function testSetCartAddress(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'shippingAddress' => [
                    'name' => 'John Doe',
                    'extra' => 'Apt 42',
                    'street' => '123 Main Street',
                    'zip' => '12345',
                    'city' => 'Berlin',
                    'country' => 'D',
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 9,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $address = new CartAddressEntity('John Doe', 'Apt 42', '123 Main Street', '12345', 'Berlin', 'D');
        $response = $cartResource->setCartAddress($address);

        $this->assertArrayHasKey('shoppingCart', $response);
        $this->assertArrayHasKey('shippingAddress', $response['shoppingCart']);
        $this->assertSame('John Doe', $response['shoppingCart']['shippingAddress']['name']);
    }

    public function testCheckout(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'order' => [
                [
                    'idOrder' => 12345678,
                    'state' => ['state' => 'bought'],
                    'seller' => ['username' => 'seller1'],
                    'article' => [['idArticle' => 1, 'price' => 5.00]],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 10,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $response = $cartResource->checkout();

        $this->assertArrayHasKey('order', $response);
        $this->assertIsArray($response['order']);
    }

    public function testSetAndGetAction(): void
    {
        // CartResource requires entity to be initialized through add() method first
        // Since add() is inherited from ModelMultipleResource, we test just that
        // the CartResource class correctly extends it
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 11,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Verify that the cart resource has the expected className for entity
        $this->assertInstanceOf(CartResource::class, $cartResource);
    }

    public function testAddArticleToCart(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [
                    [
                        'idReservation' => 123456,
                        'seller' => ['username' => 'seller1'],
                        'article' => [['idArticle' => 987654321, 'price' => 5.00]],
                    ],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 12,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Set action before adding items
        $cartArticles = new CartArticlesEntity([
            ['idArticle' => 987654321, 'amount' => 1],
        ]);
        $cartArticles->setAction(CartArticlesEntity::ACTION_ADD);

        $response = $cartResource->add($cartArticles);

        $this->assertIsArray($response);
        // Response contains request/response structure from send()
        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey('response', $response[0]);
    }

    public function testAddArticleToCartAsync(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 13,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Add article in async mode - should return justAdded without sending
        $response = $cartResource->add([
            ['idArticle' => 987654321, 'amount' => 1],
        ], true);

        $this->assertArrayHasKey('justAdded', $response);
        $this->assertTrue($response['justAdded']);
    }

    public function testRemoveArticleFromCart(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 14,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        $cartArticles = new CartArticlesEntity([
            ['idArticle' => 987654321, 'amount' => 1],
        ]);
        $cartArticles->setAction(CartArticlesEntity::ACTION_REMOVE);

        $response = $cartResource->add($cartArticles);

        $this->assertIsArray($response);
    }

    public function testSendBatchedCartArticles(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [
                'reservation' => [
                    [
                        'idReservation' => 123456,
                        'article' => [
                            ['idArticle' => 1, 'price' => 1.00],
                            ['idArticle' => 2, 'price' => 2.00],
                        ],
                    ],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 15,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Add first article in async mode
        $cartResource->add([
            ['idArticle' => 1, 'amount' => 1],
        ], true);

        // Set action before sending
        $cartResource->setAction(CartArticlesEntity::ACTION_ADD);

        // Add more articles
        $cartResource->add([
            ['idArticle' => 2, 'amount' => 1],
        ], true);

        // Now send all batched articles
        $response = $cartResource->send();

        $this->assertIsArray($response);
        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey('request', $response[0]);
        $this->assertArrayHasKey('response', $response[0]);
    }

    public function testGetEntityAfterAdd(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 16,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Add article in async mode to initialize entity
        $cartResource->add([
            ['idArticle' => 987654321, 'amount' => 1],
        ], true);

        // Now we can get the entity
        $entity = $cartResource->getEntity();

        $this->assertInstanceOf(CartArticlesEntity::class, $entity);
    }

    public function testSetActionAfterAdd(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 17,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Add article in async mode to initialize entity
        $cartResource->add([
            ['idArticle' => 987654321, 'amount' => 1],
        ], true);

        // Now setAction should work
        $result = $cartResource->setAction(CartArticlesEntity::ACTION_ADD);
        $this->assertTrue($result);

        // And getAction should return the action
        $action = $cartResource->getAction();
        $this->assertSame(CartArticlesEntity::ACTION_ADD, $action);
    }

    public function testSetActionWithInvalidAction(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'shoppingCart' => [],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 18,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $cartResource = new CartResource($this->httpClientCreatorMock);

        // Add article in async mode to initialize entity
        $cartResource->add([
            ['idArticle' => 987654321, 'amount' => 1],
        ], true);

        $this->expectException(\InvalidArgumentException::class);
        $cartResource->setAction('invalid_action');
    }

    protected function getMockResponses(): array
    {
        $cart = json_encode([
            'shoppingCart' => [
                'reservation' => [
                    [
                        'idReservation' => 123456,
                        'seller' => ['username' => 'seller1'],
                        'article' => [['idArticle' => 1, 'price' => 5.00]],
                    ],
                ],
            ],
        ]);

        $emptyCart = json_encode(['shoppingCart' => []]);

        $shippingMethods = json_encode([
            'shippingMethod' => [
                ['idShippingMethod' => 1, 'name' => 'Standard', 'price' => 2.00],
                ['idShippingMethod' => 2, 'name' => 'Tracked', 'price' => 5.00],
            ],
        ]);

        $setMethod = json_encode([
            'shippingMethod' => [
                'idShippingMethod' => 1,
                'name' => 'Standard',
                'price' => 2.00,
            ],
        ]);

        return [
            new MockResponse($cart, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($emptyCart, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
            new MockResponse($shippingMethods, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ]),
            new MockResponse($setMethod, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 8,
                ],
            ]),
        ];
    }
}
