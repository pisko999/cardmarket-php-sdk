<?php

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class GamesResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class GamesResource extends HttpCaller
{
    /**
     * Returns all games supported by MKM and you can sell and buy products for.
     *
     * @return array
     * @throws \Exception
     */
    public function getGamesList(): array
    {
        return $this->get('/games');
    }
}
