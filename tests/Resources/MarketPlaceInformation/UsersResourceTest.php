<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\MarketPlaceInformation\UsersResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class UsersResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetUserDetails()
    {
        $mockResponse = new MockResponse(json_encode([
            'user' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'isCommercial' => 0,
                'reputation' => 5,
                'sellCount' => 100,
                'soldItems' => 500,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $usersResource = new UsersResource($this->httpClientCreatorMock);

        $response = $usersResource->getUserDetails('testuser');

        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertSame('testuser', $response['user']['username']);
    }

    public function testGetRequestedUserOffers()
    {
        $mockResponse = new MockResponse(json_encode([
            'export' => [
                'idExport' => 123,
                'idUser' => 12345,
                'status' => 'ready',
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $usersResource = new UsersResource($this->httpClientCreatorMock);

        $response = $usersResource->getRequestedUserOffersById(12345);

        $this->assertArrayHasKey('export', $response);
        $this->assertIsArray($response['export']);
    }

    public function testFindUsers(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'user' => [
                [
                    'idUser' => 12345,
                    'username' => 'testuser1',
                    'isCommercial' => 0,
                    'reputation' => 5,
                ],
                [
                    'idUser' => 12346,
                    'username' => 'testuser2',
                    'isCommercial' => 1,
                    'reputation' => 4,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $usersResource = new UsersResource($this->httpClientCreatorMock);

        $response = $usersResource->findUsers('testuser');

        $this->assertArrayHasKey('user', $response);
        $this->assertIsArray($response['user']);
    }

    public function testRequestExportUserOffersById(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'export' => [
                'idExport' => 456,
                'idUser' => 12345,
                'status' => 'pending',
                'requestedAt' => '2026-01-25T10:00:00+0100',
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 8,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $usersResource = new UsersResource($this->httpClientCreatorMock);

        $response = $usersResource->requestExportUserOffersById(12345);

        $this->assertArrayHasKey('export', $response);
        $this->assertSame('pending', $response['export']['status']);
    }

    public function testGetExportUserOffersList(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'export' => [
                [
                    'idExport' => 123,
                    'idUser' => 12345,
                    'status' => 'ready',
                ],
                [
                    'idExport' => 456,
                    'idUser' => 67890,
                    'status' => 'pending',
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 9,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $usersResource = new UsersResource($this->httpClientCreatorMock);

        $response = $usersResource->getExportUserOffersList();

        $this->assertArrayHasKey('export', $response);
        $this->assertIsArray($response['export']);
    }

    protected function getMockResponses(): array
    {
        $user = json_encode([
            'user' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'isCommercial' => 0,
                'reputation' => 5,
                'sellCount' => 100,
                'soldItems' => 500,
            ],
        ]);

        $exportData = json_encode([
            'export' => [
                'status' => 'completed',
                'downloadUrl' => 'https://example.com/export.csv',
            ],
        ]);

        return [
            new MockResponse($user, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($exportData, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
        ];
    }
}
