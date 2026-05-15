<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class PricesResource.
 *
 *
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class PricesResource extends HttpCaller
{
    /**
     * Returns a price guide file in CSV format as string.
     *
     * @param int $gameId The game ID (default is 1 for Magic: The Gathering)
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return string|false
     */
    public function getPriceGuideFile(int $gameId = 1): string|false
    {
        $response = $this->get(sprintf('/priceguide?idGame=%d', $gameId));

        return gzdecode(base64_decode($response['priceguidefile']));
    }
}
