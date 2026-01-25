<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\MarketPlaceInformation\MetaproductsResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class MetaproductsResourceTest extends ResourceTestCase
{
    private MetaproductsResource $metaproductsResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->metaproductsResource = new MetaproductsResource($this->httpClientCreatorMock);
    }

    public function testGetMetaproduct()
    {
        $response = $this->metaproductsResource->getMetaProductDetails(1);

        $this->assertArrayHasKey('metaproduct', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertSame(1, $response['metaproduct']['idMetaproduct']);
    }

    public function testFindMetaProducts(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'metaproduct' => [
                [
                    'idMetaproduct' => 1,
                    'enName' => 'Black Lotus',
                    'product' => [
                        ['idProduct' => 1, 'expansion' => 'Alpha Edition'],
                        ['idProduct' => 2, 'expansion' => 'Beta Edition'],
                    ],
                ],
                [
                    'idMetaproduct' => 2,
                    'enName' => 'Black Knight',
                    'product' => [
                        ['idProduct' => 3, 'expansion' => 'Alpha Edition'],
                    ],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $metaproductsResource = new MetaproductsResource($this->httpClientCreatorMock);

        $response = $metaproductsResource->findMetaProducts('Black');

        $this->assertArrayHasKey('metaproduct', $response);
        $this->assertIsArray($response['metaproduct']);
    }

    public function testFindMetaProductsExactMatch(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'metaproduct' => [
                [
                    'idMetaproduct' => 1,
                    'enName' => 'Black Lotus',
                    'product' => [
                        ['idProduct' => 1, 'expansion' => 'Alpha Edition'],
                    ],
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $metaproductsResource = new MetaproductsResource($this->httpClientCreatorMock);

        $response = $metaproductsResource->findMetaProducts('Black Lotus', [
            'exact' => true,
            'idGame' => 1,
        ]);

        $this->assertArrayHasKey('metaproduct', $response);
    }

    protected function getMockResponses(): array
    {
        $metaproduct = json_encode([
            'metaproduct' => [
                'idMetaproduct' => 1,
                'enName' => 'Black Lotus',
                'product' => [
                    ['idProduct' => 1, 'expansion' => 'Alpha Edition'],
                    ['idProduct' => 2, 'expansion' => 'Beta Edition'],
                ],
            ],
        ]);

        return [
            new MockResponse($metaproduct, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
