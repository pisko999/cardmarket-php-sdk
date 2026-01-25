<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Entities\ArticleBaseEntity;
use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class StockResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class StockResource extends HttpCaller
{
    /**
     * Retrieves all articles in the authenticated user's stock.
     *
     * @param int $start
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getStock(int $start = 1): array
    {
        return $this->get(sprintf('/stock/%d', $start));
    }

    /**
     * Retrieve the CSV content from your own stock by Game Id.
     *
     * @param int $gameId
     * @param bool $isSealed
     * @param int $idLanguage
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getStockFile(int $gameId, bool $isSealed = false, int $idLanguage = 1): array
    {
        return $this->get(sprintf('/stock/file?idGame=%d&isSealed=%s&idLanguage=%d', $gameId, $isSealed ? 'true' : 'false', $idLanguage));
    }

    /**
     * Increase stock for the given article.
     *
     * @param int $articleId
     * @param int $stock
     *
     * @throws \Exception
     *
     * @return array
     *               The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     */
    public function increaseStock(int $articleId, int $stock): array
    {
        $article = new ArticleBaseEntity(['idArticle' => $articleId, 'amount' => $stock]);

        return $this->put('/stock/increase', $article);
    }

    /**
     * Decrease stock for the given article.
     *
     * @param int $articleId
     * @param int $stock
     *
     * @throws \Exception
     *
     * @return array
     *               The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     */
    public function decreaseStock(int $articleId, int $stock): array
    {
        $article = new ArticleBaseEntity(['idArticle' => $articleId, 'amount' => $stock]);

        return $this->put('/stock/decrease', $article);
    }

    /**
     * Returns a single article in the authenticated user's stock specified by its article ID.
     *
     * @param int $idArticle
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getStockArticle(int $idArticle): array
    {
        return $this->get(sprintf('/stock/article/%d', $idArticle));
    }

    /**
     * Searches for and returns articles specified by the article's name and game.
     *
     * @param string $name
     * @param int $idGame
     *
     * @throws \Exception
     *
     * @return array
     */
    public function findStockArticles(string $name, int $idGame): array
    {
        return $this->get(sprintf('/stock/articles/%s/%d', rawurlencode($name), $idGame));
    }

    /**
     * Returns the articles of the authenticated user's stock that belong to the specified product.
     *
     * @param int $idProduct
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getStockArticlesOfProduct(int $idProduct): array
    {
        return $this->get(sprintf('/stock/product/%d', $idProduct));
    }
}
