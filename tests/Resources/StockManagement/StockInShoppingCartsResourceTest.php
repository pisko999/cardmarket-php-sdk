<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\StockInShoppingCartsResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class StockInShoppingCartsResourceTest extends ResourceTestCase
{
    private StockInShoppingCartsResource $stockInCartsResource;

    /**
     * @var MockObject
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->stockInCartsResource = new StockInShoppingCartsResource($this->httpClientCreatorMock);
    }

    public function testGetStockInShoppingCarts()
    {
        $response = $this->stockInCartsResource->getArticlesListInUsersShoppingCarts();

        $this->assertArrayHasKey('article', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertIsArray($response['article']);
    }

    protected function getMockResponses(): array
    {
        $articlesInCarts = json_encode([
            'article' => [
                [
                    'idArticle' => 123,
                    'idProduct' => 456,
                    'count' => 2,
                    'price' => 5.00,
                    'inShoppingCart' => true,
                ],
                [
                    'idArticle' => 789,
                    'idProduct' => 1011,
                    'count' => 1,
                    'price' => 10.00,
                    'inShoppingCart' => true,
                ],
            ],
        ]);

        return [
            new MockResponse($articlesInCarts, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
