<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\WantslistEntity;

class WantslistEntityTest extends TestCase
{
    public function testCreateWantslistEntity()
    {
        $data = [
            'idWantslist' => 211682,
            'idGame' => 1,
            'name' => 'My Wantslist',
        ];

        $wantslist = new WantslistEntity($data);

        $this->assertInstanceOf(WantslistEntity::class, $wantslist);
    }

    public function testSetName()
    {
        $wantslist = new WantslistEntity();
        $wantslist->setName('Test Wantslist');

        $xml = $wantslist->getPureXML();
        $this->assertStringContainsString('<name>Test Wantslist</name>', $xml);
    }

    public function testSetIdGame()
    {
        $wantslist = new WantslistEntity();
        $wantslist->setIdGame(1);

        $xml = $wantslist->getPureXML();
        $this->assertStringContainsString('<idGame>1</idGame>', $xml);
    }

    public function testGetXML()
    {
        $wantslist = new WantslistEntity([
            'idGame' => 1,
            'name' => 'Test Wantslist',
        ]);

        $xml = $wantslist->getXML();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8" ?>', $xml);
        $this->assertStringContainsString('<request>', $xml);
        $this->assertStringContainsString('<idGame>1</idGame>', $xml);
        $this->assertStringContainsString('<name>Test Wantslist</name>', $xml);
        $this->assertStringContainsString('</request>', $xml);
    }

    public function testGetArray()
    {
        $wantslist = new WantslistEntity([
            'idGame' => 1,
            'name' => 'Test Wantslist',
        ]);

        $array = $wantslist->getArray();

        $this->assertArrayHasKey('idGame', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame(1, $array['idGame']);
        $this->assertSame('Test Wantslist', $array['name']);
    }
}
