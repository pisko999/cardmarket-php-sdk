<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\StockResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class StockResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                ['idArticle' => 123456, 'idProduct' => 100569, 'price' => 5.00],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->getStock(1);

        $this->assertArrayHasKey('article', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertIsArray($response['article']);
    }

    public function testGetStockFile()
    {
        $mockResponse = new MockResponse(json_encode([
            'stock' => 'gzipped_csv_data_here',
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->getStockFile(1, false, 1);

        $this->assertArrayHasKey('stock', $response);
    }

    public function testIncreaseStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                'idArticle' => 123456,
                'count' => 10,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->increaseStock(123456, 5);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    public function testDecreaseStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                'idArticle' => 123456,
                'count' => 3,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 8,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->decreaseStock(123456, 2);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    public function testGetStockArticle(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                'idArticle' => 123456,
                'idProduct' => 100569,
                'price' => 5.00,
                'count' => 10,
                'condition' => 'NM',
                'isFoil' => false,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 9,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->getStockArticle(123456);

        $this->assertArrayHasKey('article', $response);
        $this->assertSame(123456, $response['article']['idArticle']);
    }

    public function testFindStockArticles(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                [
                    'idArticle' => 123456,
                    'idProduct' => 100569,
                    'price' => 5.00,
                    'count' => 10,
                ],
                [
                    'idArticle' => 123457,
                    'idProduct' => 100570,
                    'price' => 3.00,
                    'count' => 5,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 10,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->findStockArticles('Black Lotus', 1);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    public function testGetStockArticlesOfProduct(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                [
                    'idArticle' => 123456,
                    'idProduct' => 100569,
                    'price' => 5.00,
                    'count' => 10,
                    'condition' => 'NM',
                ],
                [
                    'idArticle' => 123457,
                    'idProduct' => 100569,
                    'price' => 4.00,
                    'count' => 3,
                    'condition' => 'EX',
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 11,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $stockResource = new StockResource($this->httpClientCreatorMock);

        $response = $stockResource->getStockArticlesOfProduct(100569);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    protected function getMockResponses(): array
    {
        $stock = json_encode([
            'article' => [
                ['idArticle' => 1, 'idProduct' => 100, 'price' => 5.00, 'count' => 10],
                ['idArticle' => 2, 'idProduct' => 101, 'price' => 10.00, 'count' => 5],
            ],
        ]);

        $csvContent = "idArticle,idProduct,English Name,Count,Price\n1,100,Test Card,10,5.00";
        $encoded = base64_encode(gzencode($csvContent));
        $stockFile = json_encode(['stock' => $encoded]);

        $articleUpdated = json_encode([
            'article' => [
                'idArticle' => 123456,
                'count' => 15,
                'price' => 5.00,
            ],
        ]);

        return [
            new MockResponse($stock, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($stockFile, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
            new MockResponse($articleUpdated, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ]),
            new MockResponse($articleUpdated, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 8,
                ],
            ]),
        ];
    }
}
