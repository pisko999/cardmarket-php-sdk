<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\MessageEntity;

class MessageEntityTest extends TestCase
{
    public function testCreateMessageEntity()
    {
        $message = new MessageEntity('Hello, this is a test message');

        $this->assertInstanceOf(MessageEntity::class, $message);
    }

    public function testMessageEntityGetXML()
    {
        $message = new MessageEntity('Test message content');
        $xml = $message->getXML();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8" ?>', $xml);
        $this->assertStringContainsString('<request>', $xml);
        $this->assertStringContainsString('Test message content', $xml);
        $this->assertStringContainsString('</request>', $xml);
    }
}
