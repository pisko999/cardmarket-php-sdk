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
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class StockExportResource extends HttpCaller
{
    /**
     * Request the export of Article entities from your stock
     *
     * @param int idGame
     *
     * @return array
     * @throws \Exception
     */
    public function askStockExport(?int $idGame = null): array
    {
        $url = '/exports/stock';
        if ($idGame) {
            $url .= '?idGame=' . $idGame;
        }
        return $this->post($url);
    }

    public function getStockExportStatus(): array
    {
        return $this->get('/exports/stock');
    }
}
