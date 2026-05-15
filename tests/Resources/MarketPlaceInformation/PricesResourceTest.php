<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\MarketPlaceInformation\PricesResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class PricesResourceTest extends ResourceTestCase
{
    private PricesResource $pricesResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->pricesResource = new PricesResource($this->httpClientCreatorMock);
    }

    public function testGetPriceGuideFile()
    {
        $response = $this->pricesResource->getPriceGuideFile();

        $this->assertIsString($response);
        $this->assertStringContainsString('idProduct', $response);
    }

    public function testGetPriceGuideFileWithGameId()
    {
        $response = $this->pricesResource->getPriceGuideFile(3);

        $this->assertIsString($response);
        $this->assertStringContainsString('idProduct', $response);
    }

    protected function getMockResponses(): array
    {
        $csvContent = "idProduct,Price_AVG,Price_LOW\n123,5.50,3.00\n456,10.00,7.50";
        $encoded = base64_encode(gzencode($csvContent));

        $priceGuide = json_encode([
            'priceguidefile' => $encoded,
        ]);

        return [
            new MockResponse($priceGuide, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
        ];
    }
}
