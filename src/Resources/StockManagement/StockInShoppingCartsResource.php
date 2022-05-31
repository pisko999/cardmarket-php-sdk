<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class StockInShoppingCartsResource
 *
 * @package Pisko\CardMarket\Resources\StockManagement
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class StockInShoppingCartsResource extends HttpCaller
{
    /**
     * Returns the Article entities of the authenticated user's stock that are
     * currently in other user's shopping carts.
     *
     * @return array
     * @throws \Exception
     */
    public function getArticlesListInUsersShoppingCarts(): array
    {
        return $this->get('/stock/shoppingcart-articles');
    }
}
