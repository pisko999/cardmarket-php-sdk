<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\WantslistItemEntity;
use Pisko\CardMarket\Entities\WantslistItemsEntity;

class WantslistItemsEntityTest extends TestCase
{
    public function testCreateWantslistItemsEntity()
    {
        $item1 = new WantslistItemEntity(['idProduct' => 100, 'count' => 1]);
        $item2 = new WantslistItemEntity(['idProduct' => 200, 'count' => 2]);

        $items = new WantslistItemsEntity([$item1, $item2]);

        $this->assertInstanceOf(WantslistItemsEntity::class, $items);
    }

    public function testSetActionAddItem()
    {
        $item = new WantslistItemEntity(['idProduct' => 100, 'count' => 1]);
        $items = new WantslistItemsEntity([$item]);

        $result = $items->setAction(WantslistItemsEntity::ACTION_ADDITEM);

        $this->assertTrue($result);
        $this->assertSame(WantslistItemsEntity::ACTION_ADDITEM, $items->getAction());
    }

    public function testSetActionEditItem()
    {
        $item = new WantslistItemEntity(['idWant' => 123, 'count' => 2]);
        $items = new WantslistItemsEntity([$item]);

        $result = $items->setAction(WantslistItemsEntity::ACTION_EDITITEM);

        $this->assertTrue($result);
        $this->assertSame(WantslistItemsEntity::ACTION_EDITITEM, $items->getAction());
    }

    public function testSetActionDeleteItem()
    {
        $item = new WantslistItemEntity(['idWant' => 123, 'count' => 1]);
        $items = new WantslistItemsEntity([$item]);

        $result = $items->setAction(WantslistItemsEntity::ACTION_DELETEITEM);

        $this->assertTrue($result);
        $this->assertSame(WantslistItemsEntity::ACTION_DELETEITEM, $items->getAction());
    }

    public function testSetInvalidActionThrowsException()
    {
        $item = new WantslistItemEntity(['idProduct' => 100, 'count' => 1]);
        $items = new WantslistItemsEntity([$item]);

        $this->expectException(\InvalidArgumentException::class);
        $items->setAction('invalidAction');
    }

    public function testGetAditionalXMLWithAction()
    {
        $item = new WantslistItemEntity(['idProduct' => 100, 'count' => 1]);
        $items = new WantslistItemsEntity([$item]);
        $items->setAction(WantslistItemsEntity::ACTION_ADDITEM);

        $xml = $items->getAditionalXML();

        $this->assertStringContainsString('<action>addItem</action>', $xml);
    }

    public function testGetAditionalXMLWithoutActionThrowsException()
    {
        $item = new WantslistItemEntity(['idProduct' => 100, 'count' => 1]);
        $items = new WantslistItemsEntity([$item]);

        $this->expectException(\LogicException::class);
        $items->getAditionalXML();
    }
}
