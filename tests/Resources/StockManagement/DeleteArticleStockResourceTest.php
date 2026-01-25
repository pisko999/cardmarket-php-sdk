<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\DeleteArticleStockResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class DeleteArticleStockResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDeleteArticleFromStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'deleted' => [
                [
                    'idArticle' => 123456,
                    'count' => 1,
                    'success' => true,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $deleteStockResource = new DeleteArticleStockResource($this->httpClientCreatorMock);

        $articles = [
            [
                'idArticle' => 123456,
                'count' => 1,
            ],
        ];

        $response = $deleteStockResource->add($articles);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('response', $response[0]);
    }

    public function testDeleteMultipleArticles()
    {
        $mockResponse = new MockResponse(json_encode([
            'deleted' => [
                [
                    'idArticle' => 123456,
                    'count' => 1,
                    'success' => true,
                ],
                [
                    'idArticle' => 789012,
                    'count' => 2,
                    'success' => true,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $deleteStockResource = new DeleteArticleStockResource($this->httpClientCreatorMock);

        $articles = [
            ['idArticle' => 123456, 'count' => 1],
            ['idArticle' => 789012, 'count' => 2],
        ];

        $response = $deleteStockResource->add($articles);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    protected function getMockResponses(): array
    {
        $deletedArticle = json_encode([
            'deleted' => [
                [
                    'idArticle' => 123456,
                    'count' => 1,
                    'success' => true,
                ],
            ],
        ]);

        $deletedMultiple = json_encode([
            'deleted' => [
                [
                    'idArticle' => 123456,
                    'count' => 1,
                    'success' => true,
                ],
                [
                    'idArticle' => 789012,
                    'count' => 2,
                    'success' => true,
                ],
            ],
        ]);

        return [
            new MockResponse($deletedArticle, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($deletedMultiple, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
        ];
    }
}
