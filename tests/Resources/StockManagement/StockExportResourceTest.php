<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\StockManagement;

use Pisko\CardMarket\Resources\StockManagement\StockExportResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class StockExportResourceTest extends ResourceTestCase
{
    private StockExportResource $stockExportResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->stockExportResource = new StockExportResource($this->httpClientCreatorMock);
    }

    public function testAskStockExport()
    {
        $response = $this->stockExportResource->askStockExport(1);

        $this->assertArrayHasKey('export', $response);
        $this->assertArrayHasKey('api', $response);
    }

    public function testAskStockExportAllGames()
    {
        $response = $this->stockExportResource->askStockExport();

        $this->assertArrayHasKey('export', $response);
    }

    public function testGetStockExportStatus()
    {
        $response = $this->stockExportResource->getStockExportStatus();

        $this->assertArrayHasKey('export', $response);
        $this->assertIsArray($response['export']);
    }

    protected function getMockResponses(): array
    {
        $exportRequested = json_encode([
            'export' => [
                'idExport' => 'exp123',
                'status' => 'requested',
                'idGame' => 1,
                'requestedAt' => '2026-01-25T10:00:00+0100',
            ],
        ]);

        $exportStatus = json_encode([
            'export' => [
                [
                    'idExport' => 'exp123',
                    'status' => 'completed',
                    'idGame' => 1,
                    'downloadUrl' => 'https://example.com/export.csv',
                ],
            ],
        ]);

        return [
            new MockResponse($exportRequested, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($exportRequested, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
            new MockResponse($exportStatus, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ]),
        ];
    }
}
