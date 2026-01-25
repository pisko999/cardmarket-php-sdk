<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class GamesResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class GamesResource extends HttpCaller
{
    /**
     * Returns all games supported by MKM and you can sell and buy products for.
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getGamesList(): array
    {
        return $this->get('/games');
    }
}
