<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\WantsListManagement;

use Pisko\CardMarket\Entities\WantslistItemEntity;
use Pisko\CardMarket\Entities\WantslistItemsEntity;
use Pisko\CardMarket\Resources\WantsListManagement\WantsListResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class WantsListResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetWantsLists()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                [
                    'idWantslist' => 211682,
                    'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                    'name' => 'Main Wantslist for MtG',
                    'itemCount' => 5,
                ],
                [
                    'idWantslist' => 211683,
                    'game' => ['idGame' => 6, 'name' => 'Pokemon'],
                    'name' => 'Pokemon Wantslist',
                    'itemCount' => 3,
                ],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $response = $resource->getWantsLists();

        $this->assertArrayHasKey('wantslist', $response);
        $this->assertArrayHasKey('api', $response);
        $this->assertIsArray($response['wantslist']);
        $this->assertCount(2, $response['wantslist']);
    }

    public function testGetWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211682,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'Main Wantslist for MtG',
                'itemCount' => 1,
                'item' => [
                    [
                        'idWant' => 12345,
                        'count' => 2,
                        'wishPrice' => 5,
                        'idProduct' => 100569,
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
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $response = $resource->getWantsList(211682);

        $this->assertArrayHasKey('wantslist', $response);
        $this->assertIsArray($response['wantslist']);
        $this->assertSame(211682, $response['wantslist']['idWantslist']);
    }

    public function testCreateWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211684,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'My Test Wantslist',
                'itemCount' => 0,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $response = $resource->createWantsList('My Test Wantslist', 1);

        $this->assertArrayHasKey('wantslist', $response);
        $this->assertIsArray($response['wantslist']);
        $this->assertSame('My Test Wantslist', $response['wantslist']['name']);
    }

    public function testRenameWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211682,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'Renamed Wantslist',
                'itemCount' => 1,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 8,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $response = $resource->renameWantsList(211682, 'Renamed Wantslist');

        $this->assertArrayHasKey('wantslist', $response);
        $this->assertIsArray($response['wantslist']);
        $this->assertSame('Renamed Wantslist', $response['wantslist']['name']);
    }

    public function testDeleteWantsList()
    {
        $mockResponse = new MockResponse(json_encode(['deleted' => true]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 9,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $response = $resource->deleteWantsList(211682);

        $this->assertArrayHasKey('deleted', $response);
        $this->assertTrue($response['deleted']);
    }

    public function testAddItemsToWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211682,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'Main Wantslist for MtG',
                'itemCount' => 2,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 10,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $item = new WantslistItemEntity([
            'idProduct' => 100569,
            'count' => 2,
            'wishPrice' => 5,
            'idLanguage' => 1,
            'minCondition' => 'NM',
        ]);

        $items = new WantslistItemsEntity([$item]);
        $response = $resource->addItemsToWantsList(211682, $items);

        $this->assertArrayHasKey('wantslist', $response);
    }

    public function testEditItemsInWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211682,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'Main Wantslist for MtG',
                'itemCount' => 2,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 11,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $item = new WantslistItemEntity([
            'idWant' => 12345,
            'count' => 3,
            'wishPrice' => 10,
        ]);

        $items = new WantslistItemsEntity([$item]);
        $response = $resource->editItemsInWantsList(211682, $items);

        $this->assertArrayHasKey('wantslist', $response);
    }

    public function testDeleteItemsFromWantsList()
    {
        $mockResponse = new MockResponse(json_encode([
            'wantslist' => [
                'idWantslist' => 211682,
                'game' => ['idGame' => 1, 'name' => 'Magic the Gathering'],
                'name' => 'Main Wantslist for MtG',
                'itemCount' => 0,
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 12,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $resource = new WantsListResource($this->httpClientCreatorMock);

        $item = new WantslistItemEntity([
            'idWant' => 12345,
            'count' => 1,
        ]);

        $items = new WantslistItemsEntity([$item]);
        $response = $resource->deleteItemsFromWantsList(211682, $items);

        $this->assertArrayHasKey('wantslist', $response);
    }

    protected function getMockResponses(): array
    {
        return [];
    }
}
