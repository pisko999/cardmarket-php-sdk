<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Tests;

use Pisko\CardMarket\Cardmarket;
use Pisko\CardMarket\HttpClient\HttpClientCreator;
use Pisko\CardMarket\Resources\MarketPlaceInformation\ExpansionsResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Pisko\CardMarket\Resources\StockManagement\StockInShoppingCartsResource;
use Pisko\CardMarket\Resources\StockManagement\StockResource;
use PHPUnit\Framework\TestCase;

class CardmarketTest extends TestCase
{

    private $cardmarket;

    public function setUp(): void
    {
        parent::setUp();

        $httpClientCreatorMock = $this->createMock(HttpClientCreator::class);
        $this->cardmarket = new Cardmarket($httpClientCreatorMock);
    }

    public function testCheckAccessToDefaultResources()
    {
        $this->assertInstanceOf(GamesResource::class, $this->cardmarket->games());
        $this->assertInstanceOf(ExpansionsResource::class, $this->cardmarket->expansions());
        $this->assertInstanceOf(ProductsResource::class, $this->cardmarket->products());
        $this->assertInstanceOf(StockResource::class, $this->cardmarket->stock());
        $this->assertInstanceOf(StockInShoppingCartsResource::class, $this->cardmarket->stockInShoppingCarts());
    }

    public function testToRegisterDefaultResource()
    {
        $this->expectException(\LogicException::class);
        $this->cardmarket->registerResources("games", \stdClass::class);
    }

    public function testToRegisterNewResource()
    {
        $this->cardmarket->registerResources("new", \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $this->cardmarket->new());
    }

    public function testThrowAnExceptionIfMethodDoesntExists()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->cardmarket->fake();
    }

}
