<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ProductsResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRetrieveProductDetails()
    {
        $this->setupHttpClientCreatorMock();
        $productsResource = new ProductsResource($this->httpClientCreatorMock);

        $response = $productsResource->getProductDetails(273799);

        $this->assertArrayHasKey('product', $response);
        $this->assertArrayHasKey('api', $response);

        $this->assertSame(5000, (int) $response['api']['request-limit-max']);
        $this->assertSame(1, (int) $response['api']['request-limit-count']);

        $propertiesToCheck = ['idProduct', 'countReprints', 'enName', 'image', 'gameName', 'idGame', 'number', 'rarity',
            'priceGuide', 'countArticles', 'countFoils'];

        foreach ($propertiesToCheck as $keyName) {
            $this->assertArrayHasKey($keyName, $response['product']);
        }
    }

    public function testRetrieveProductListFile()
    {
        $mockResponse = new MockResponse(
            file_get_contents(sprintf(__DIR__ . '/../MockResponse/productList.json')),
            [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 1,
                ],
            ],
        );

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $productsResource = new ProductsResource($this->httpClientCreatorMock);

        $response = $productsResource->getProductListFile();

        $this->assertSame('"246478","Deathwhisper","3","WOW Single","1268","203180","2007-01-01 00:00:00"
"246479","Ashbringer","3","WOW Single","1268","203181","2007-01-01 00:00:00"
"246480","Staff of Antonidas","3","WOW Single","1268","203182","2007-01-01 00:00:00"
"246481","Lord Marrowgar","3","WOW Single","1268","203183","2007-01-01 00:00:00"
"246482","Lady Deathwhisper","3","WOW Single","1268","203184","2007-01-01 00:00:00"
"246483","Deathbringer Saurfang","3","WOW Single","1268","203185","2007-01-01 00:00:00"
"246484","Rotface","3","WOW Single","1268","203186","2007-01-01 00:00:00"
"246485","Festergut","3","WOW Single","1268","203187","2007-01-01 00:00:00"
', $response);
    }

    public function testFindProducts()
    {
        $mockResponse = new MockResponse(
            file_get_contents(sprintf(__DIR__ . '/../MockResponse/findProducts_pernicious_deed.json')),
            [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ],
        );

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $productsResource = new ProductsResource($this->httpClientCreatorMock);

        $response = $productsResource->findProducts('perinici');

        $this->assertArrayHasKey('product', $response);
        $this->assertArrayHasKey('api', $response);

        $this->assertSame(5000, (int) $response['api']['request-limit-max']);
        $this->assertSame(7, (int) $response['api']['request-limit-count']);

        $propertiesToCheck = ['idProduct', 'countReprints', 'enName', 'image', 'gameName', 'idGame', 'number', 'rarity',
            'countArticles', 'countFoils'];

        for ($i = 0; $i < 3; $i++) {
            foreach ($propertiesToCheck as $keyName) {
                $this->assertArrayHasKey($keyName, $response['product'][$i]);
            }
        }
    }

    protected function getMockResponses(): array
    {
        return [
          new MockResponse(
              file_get_contents(__DIR__ . '/../MockResponse/product_273799.json'),
              [
                'response_headers' => [
                  'X-Request-Limit-Max' => 5000,
                  'X-Request-Limit-Count' => 1,
                ],
            ],
          ),
        ];
    }
}
