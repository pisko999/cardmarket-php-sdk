<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\ArticleEntity;

class ArticleEntityTest extends TestCase
{
    public function testCreateArticleEntity()
    {
        $data = [
            'idArticle' => 123456,
            'idProduct' => 100569,
            'idLanguage' => 1,
            'comments' => 'Test comment',
            'count' => 5,
            'price' => 10.50,
            'condition' => 'NM',
            'isFoil' => true,
            'isSigned' => false,
            'isAltered' => false,
        ];

        $article = new ArticleEntity($data);

        $this->assertInstanceOf(ArticleEntity::class, $article);
        $this->assertSame(10.50, $article->getPrice());
    }

    public function testArticleEntityGetPureXML()
    {
        $data = [
            'idProduct' => 100569,
            'idLanguage' => 1,
            'comments' => 'Test',
            'count' => 1,
            'price' => 5.00,
            'condition' => 'NM',
            'isFoil' => true,
        ];

        $article = new ArticleEntity($data);
        $xml = $article->getPureXML();

        $this->assertStringContainsString('<article>', $xml);
        $this->assertStringContainsString('<idProduct>100569</idProduct>', $xml);
        $this->assertStringContainsString('<isFoil>true</isFoil>', $xml);
        $this->assertStringContainsString('</article>', $xml);
    }

    public function testArticleEntityIsMe()
    {
        $data = [
            'idArticle' => 123456,
            'idProduct' => 100569,
            'price' => 5.00,
            'condition' => 'NM',
        ];

        $article = new ArticleEntity($data);

        $this->assertTrue($article->isMe(['idProduct' => 100569, 'price' => 5.00]));
        $this->assertFalse($article->isMe(['idProduct' => 999, 'price' => 5.00]));
    }

    public function testArticleEntityError()
    {
        $data = ['idArticle' => 123, 'count' => 1];
        $article = new ArticleEntity($data);

        $this->assertFalse($article->hasError());

        $article->setError();
        $this->assertTrue($article->hasError());
    }
}
