<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\UpdateArticleStockResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class UpdateArticleStockResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUpdateArticleInStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'updatedArticles' => [
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
        $updateStockResource = new UpdateArticleStockResource($this->httpClientCreatorMock);

        $articles = [
            [
                'idArticle' => 123456,
                'idLanguage' => 1,
                'comments' => 'Updated article',
                'count' => 1,
                'price' => 6.50,
                'condition' => 'EX',
                'isFoil' => false,
            ],
        ];

        $response = $updateStockResource->add($articles);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('response', $response[0]);
    }

    protected function getMockResponses(): array
    {
        $updatedArticle = json_encode([
            'updatedArticles' => [
                [
                    'idArticle' => 123456,
                    'count' => 1,
                    'success' => true,
                ],
            ],
        ]);

        return [
            new MockResponse($updatedArticle, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
