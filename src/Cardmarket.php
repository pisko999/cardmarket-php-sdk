<?php
declare(strict_types=1);

namespace Pisko\CardMarket;

use Pisko\CardMarket\Exception\NonExistsResourceException;
use Pisko\CardMarket\HttpClient\HttpClientCreator;
use Pisko\CardMarket\Resources\AccountManagement\MessagesResource;
use Pisko\CardMarket\Resources\HttpCaller;
use Pisko\CardMarket\Resources\MarketPlaceInformation\ArticlesResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\ExpansionsResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\GamesResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\MetaproductsResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\PricesResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\ProductsResource;
use Pisko\CardMarket\Resources\MarketPlaceInformation\UsersResource;
use Pisko\CardMarket\Resources\OrdersManagement\OrdersResource;
use Pisko\CardMarket\Resources\StockManagement\AddArticleStockResource;
use Pisko\CardMarket\Resources\StockManagement\DeleteArticleStockResource;
use Pisko\CardMarket\Resources\StockManagement\StockExportResource;
use Pisko\CardMarket\Resources\StockManagement\StockInShoppingCartsResource;
use Pisko\CardMarket\Resources\StockManagement\StockResource;
use Pisko\CardMarket\Resources\StockManagement\UpdateArticleStockResource;
use Spatie\Macroable\Macroable;

/**
 * Class Cardmarket
 *
 * @package Pisko\CardMarket
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
class Cardmarket
{
    use Macroable;

    private HttpClientCreator $httpClientCreator;

    private array $resources;

    public function __construct(HttpClientCreator $httpClientCreator)
    {
        $this->httpClientCreator = $httpClientCreator;
        $this->resources = [];
    }

    public function messages(): MessagesResource
    {
        return $this->getResource(MessagesResource::class);
    }

    public function articles(): ArticlesResource
    {
        return $this->getResource(ArticlesResource::class);
    }

    public function addArticleStock(): AddArticleStockResource
    {
        return $this->getResource(AddArticleStockResource::class);
    }

    public function updateArticleStock(): UpdateArticleStockResource
    {
        return $this->getResource(UpdateArticleStockResource::class);
    }

    public function deleteArticleStock(): DeleteArticleStockResource
    {
        return $this->getResource(DeleteArticleStockResource::class);
    }

    public function games(): GamesResource
    {
        return $this->getResource(GamesResource::class);
    }

    public function expansions(): ExpansionsResource
    {
        return $this->getResource(ExpansionsResource::class);
    }

    public function metaproducts(): MetaproductsResource
    {
        return $this->getResource(MetaproductsResource::class);
    }

    public function orders(): OrdersResource
    {
        return $this->getResource(OrdersResource::class);
    }

    public function prices(): PricesResource
    {
        return $this->getResource(PricesResource::class);
    }

    public function products(): ProductsResource
    {
        return $this->getResource(ProductsResource::class);
    }

    public function stock(): StockResource
    {
        return $this->getResource(StockResource::class);
    }

    public function StockExport(): StockExportResource
    {
        return $this->getResource(StockExportResource::class);
    }

    public function stockInShoppingCarts(): StockInShoppingCartsResource
    {
        return $this->getResource(StockInShoppingCartsResource::class);
    }

    public function users(): UsersResource
    {
        return $this->getResource(UsersResource::class);
    }

    /**
     * Register custom resources on Cardmarket wrapper.
     *
     * @param string $methodName
     * @param string $fqcn
     */
    public function registerResources(string $methodName, string $fqcn): void
    {
        if (in_array($methodName, $this->getDefaultResources())) {
            throw new \LogicException(sprintf("You can't override default resources (%s)", implode(', ', array_values($this->getDefaultResources()))));
        }

        $httpClientCreator = $this->httpClientCreator;

        self::macro($methodName, function () use ($httpClientCreator, $fqcn) {
            return new $fqcn($httpClientCreator);
        });
    }

    /**
     * Default methods names to access Cardmarket Resources.
     *
     * @return array
     */
    private function getDefaultResources(): array
    {
        return ["games", "expansions", "cards", "stock", "stockInShoppingCarts"];
    }


    /**
     * @throws NonExistsResourceException
     */
    private function getResource(string $name): mixed {
        if (!class_exists($name)) {
            throw new NonExistsResourceException();
        }
        if (!isset($this->resources[$name])) {
            $this->resources[$name] = new $name($this->httpClientCreator);
        }
        return $this->resources[$name];
    }
}
