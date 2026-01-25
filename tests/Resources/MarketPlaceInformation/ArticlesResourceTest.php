<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\MarketPlaceInformation\ArticlesResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ArticlesResourceTest extends ResourceTestCase
{
    private ArticlesResource $articlesResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->articlesResource = new ArticlesResource($this->httpClientCreatorMock);
    }

    public function testGetArticles()
    {
        $response = $this->articlesResource->getArticles(266361);

        $this->assertArrayHasKey('article', $response);
        $this->assertArrayHasKey('api', $response);
    }

    public function testGetArticlesByUser(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                [
                    'idArticle' => 987654321,
                    'idProduct' => 266361,
                    'language' => ['idLanguage' => 1, 'languageName' => 'English'],
                    'price' => 1.50,
                    'count' => 4,
                    'condition' => 'EX',
                    'isFoil' => true,
                ],
                [
                    'idArticle' => 987654322,
                    'idProduct' => 266362,
                    'language' => ['idLanguage' => 1, 'languageName' => 'English'],
                    'price' => 2.00,
                    'count' => 2,
                    'condition' => 'NM',
                    'isFoil' => false,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $articlesResource = new ArticlesResource($this->httpClientCreatorMock);

        $response = $articlesResource->getArticlesByUser(12345);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    public function testGetArticlesByUserWithFilters(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'article' => [
                [
                    'idArticle' => 987654321,
                    'idProduct' => 266361,
                    'price' => 1.50,
                    'condition' => 'NM',
                    'isFoil' => true,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $articlesResource = new ArticlesResource($this->httpClientCreatorMock);

        $response = $articlesResource->getArticlesByUser(12345, 0, 50, [
            'idGame' => 1,
            'isFoil' => true,
            'minCondition' => 'NM',
        ]);

        $this->assertArrayHasKey('article', $response);
        $this->assertIsArray($response['article']);
    }

    protected function getMockResponses(): array
    {
        $article = json_encode([
            'article' => [
                'idArticle' => 142158699,
                'idProduct' => 266361,
                'language' => ['idLanguage' => 1, 'languageName' => 'English'],
                'price' => 0.50,
                'count' => 1,
                'condition' => 'NM',
                'isFoil' => false,
                'isSigned' => false,
            ],
        ]);

        return [
            new MockResponse($article, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
