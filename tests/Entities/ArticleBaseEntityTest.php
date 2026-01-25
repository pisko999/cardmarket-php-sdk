<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\ArticleBaseEntity;

class ArticleBaseEntityTest extends TestCase
{
    public function testCreateArticleBaseEntity()
    {
        $data = [
            'idArticle' => 123456,
            'amount' => 5,
        ];

        $article = new ArticleBaseEntity($data);

        $this->assertInstanceOf(ArticleBaseEntity::class, $article);
    }

    public function testGetPureXML()
    {
        $data = [
            'idArticle' => 123456,
            'amount' => 5,
        ];

        $article = new ArticleBaseEntity($data);
        $xml = $article->getPureXML();

        $this->assertStringContainsString('<article>', $xml);
        $this->assertStringContainsString('<idArticle>123456</idArticle>', $xml);
        $this->assertStringContainsString('<amount>5</amount>', $xml);
        $this->assertStringContainsString('</article>', $xml);
    }

    public function testGetArray()
    {
        $data = [
            'idArticle' => 123456,
            'amount' => 5,
        ];

        $article = new ArticleBaseEntity($data);
        $array = $article->getArray();

        $this->assertArrayHasKey('idArticle', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertSame(123456, $array['idArticle']);
        $this->assertSame(5, $array['amount']);
    }
}
