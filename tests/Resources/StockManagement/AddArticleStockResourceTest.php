<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\AddArticleStockResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class AddArticleStockResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAddArticleToStock()
    {
        $mockResponse = new MockResponse(json_encode([
            'inserted' => [
                [
                    'idArticle' => 142158699,
                    'idProduct' => 100569,
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
        $addStockResource = new AddArticleStockResource($this->httpClientCreatorMock);

        $articles = [
            [
                'idProduct' => 100569,
                'idLanguage' => 1,
                'comments' => 'Test article',
                'count' => 1,
                'price' => 5.00,
                'condition' => 'NM',
                'isFoil' => false,
            ],
        ];

        $response = $addStockResource->add($articles);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('response', $response[0]);
    }

    protected function getMockResponses(): array
    {
        $addedArticle = json_encode([
            'inserted' => [
                [
                    'idArticle' => [
                        'idArticle' => 123456,
                    ],
                    'success' => true,
                ],
            ],
        ]);

        return [
            new MockResponse($addedArticle, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
