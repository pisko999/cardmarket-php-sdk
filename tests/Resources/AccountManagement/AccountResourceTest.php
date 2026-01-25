<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\AccountManagement;

use Pisko\CardMarket\Resources\AccountManagement\AccountResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class AccountResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetAccountInformation()
    {
        $mockResponse = new MockResponse(json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => false,
                'idDisplayLanguage' => 1,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $accountResource = new AccountResource($this->httpClientCreatorMock);

        $response = $accountResource->getAccountInformation();

        $this->assertArrayHasKey('account', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertSame(5000, (int) $response['api']['request-limit-max']);
        $this->assertSame(5, (int) $response['api']['request-limit-count']);
    }

    public function testSetOnVacation()
    {
        $mockResponse = new MockResponse(json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => true,
                'idDisplayLanguage' => 1,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $accountResource = new AccountResource($this->httpClientCreatorMock);

        $response = $accountResource->setOnVacation(true, [
            'cancelOrders' => true,
            'relistItems' => true,
        ]);

        $this->assertArrayHasKey('account', $response);
        $this->assertTrue($response['account']['onVacation']);
    }

    public function testSetDisplayLanguage()
    {
        $mockResponse = new MockResponse(json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => false,
                'idDisplayLanguage' => 1,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $accountResource = new AccountResource($this->httpClientCreatorMock);

        $response = $accountResource->setDisplayLanguage(1);

        $this->assertArrayHasKey('account', $response);
        $this->assertSame(1, $response['account']['idDisplayLanguage']);
    }

    protected function getMockResponses(): array
    {
        $accountInfo = json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => false,
                'idDisplayLanguage' => 1,
            ],
        ]);

        $vacationSet = json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => true,
                'idDisplayLanguage' => 1,
            ],
        ]);

        $languageSet = json_encode([
            'account' => [
                'idUser' => 12345,
                'username' => 'testuser',
                'onVacation' => false,
                'idDisplayLanguage' => 1,
            ],
        ]);

        return [
            new MockResponse($accountInfo, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($vacationSet, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
            new MockResponse($languageSet, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ]),
        ];
    }
}
