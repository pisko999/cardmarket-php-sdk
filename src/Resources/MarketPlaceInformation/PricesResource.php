<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class PricesResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class PricesResource extends HttpCaller
{
    /**
     * Returns a price guide file in CSV format as string.
     *
     * @return string|false
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getPriceGuideFile(): string|false
    {
        $response = $this->get(sprintf('/priceguide'));
        return gzdecode(base64_decode($response['priceguidefile']));
    }
}
