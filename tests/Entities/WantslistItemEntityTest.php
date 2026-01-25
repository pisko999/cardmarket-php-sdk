<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\WantslistItemEntity;

class WantslistItemEntityTest extends TestCase
{
    public function testCreateWantslistItemWithProduct()
    {
        $data = [
            'idProduct' => 100569,
            'count' => 2,
            'wishPrice' => 5,
            'idLanguage' => 1,
            'minCondition' => 'NM',
            'isFoil' => true,
        ];

        $item = new WantslistItemEntity($data);

        $this->assertInstanceOf(WantslistItemEntity::class, $item);
    }

    public function testCreateWantslistItemWithMetaproduct()
    {
        $data = [
            'idMetaproduct' => 12345,
            'count' => 1,
            'wishPrice' => 10,
            'idLanguage' => 1,
        ];

        $item = new WantslistItemEntity($data);

        $this->assertInstanceOf(WantslistItemEntity::class, $item);
    }

    public function testGetPureXMLWithProduct()
    {
        $data = [
            'idProduct' => 100569,
            'count' => 2,
            'wishPrice' => 5,
            'idLanguage' => 1,
            'minCondition' => 'NM',
            'isFoil' => true,
        ];

        $item = new WantslistItemEntity($data);
        $xml = $item->getPureXML();

        $this->assertStringContainsString('<want>', $xml);
        $this->assertStringContainsString('<idProduct>100569</idProduct>', $xml);
        $this->assertStringContainsString('<count>2</count>', $xml);
        $this->assertStringContainsString('<isFoil>true</isFoil>', $xml);
        $this->assertStringContainsString('</want>', $xml);
    }

    public function testGetPureXMLWithMetaproduct()
    {
        $data = [
            'idMetaproduct' => 12345,
            'count' => 1,
            'wishPrice' => 10,
        ];

        $item = new WantslistItemEntity($data);
        $xml = $item->getPureXML();

        $this->assertStringContainsString('<idMetaproduct>12345</idMetaproduct>', $xml);
        $this->assertStringNotContainsString('<idProduct>', $xml);
    }

    public function testGetArray()
    {
        $data = [
            'idProduct' => 100569,
            'count' => 2,
            'wishPrice' => 5,
            'isFoil' => true,
        ];

        $item = new WantslistItemEntity($data);
        $array = $item->getArray();

        $this->assertArrayHasKey('idProduct', $array);
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('wishPrice', $array);
        $this->assertArrayHasKey('isFoil', $array);
        $this->assertTrue($array['isFoil']);
    }
}
