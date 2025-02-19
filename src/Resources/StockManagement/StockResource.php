<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Entities\ArticleBaseEntity;
use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class StockInShoppingCartsResource
 *
 * @package Pisko\CardMarket\Resources\StockManagement
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
     * @return array
     * @throws \Exception
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
     * @return array
     * @throws \Exception
     */
    public function getStockFile(int $gameId, bool $isSealed = false, int $idLanguage = 1): array
    {
        return $this->get(sprintf('/stock/file?idGame=%d&isSealed=%s&idLanguage=%d', $gameId, $isSealed, $idLanguage));
    }

    /**
     * Increase stock for the given article.
     *
     * @param int $articleId
     * @param int $stock
     *
     * @return array
     *      The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     * @throws \Exception
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
     * @return array
     *      The Article object into array. (https://api.cardmarket.com/ws/documentation/API_2.0:Entities:Article)
     * @throws \Exception
     */
    public function decreaseStock(int $articleId, int $stock): array
    {
        $article = new ArticleBaseEntity(['idArticle' => $articleId, 'amount' => $stock]);
        return $this->put('/stock/decrease', $article);
    }
}
