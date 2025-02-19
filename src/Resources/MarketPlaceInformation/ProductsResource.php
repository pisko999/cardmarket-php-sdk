<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class ProductsResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class ProductsResource extends HttpCaller
{
    /**
     * Returns a product specified by its ID.
     *
     * @param int $productId
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getProductDetails(int $productId): array
    {
        return $this->get(sprintf('/products/%d', $productId));
    }

    /**
     * Returns a product file in CSV format as string.
     *
     * @return string|false
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getProductListFile(): string|false
    {
        $response = $this->get(sprintf('/productlist'));
        return gzdecode(base64_decode($response['productsfile']));
    }

    /**
     * Find products by name
     *
     * @param string $search
     * @param int $start
     * @param int $maxResults
     * @param array $searchData
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function findProducts(
        string $search,
        int $start = 0,
        int $maxResults = 100,
        array $searchData = []
    ): array
    {
        $optional = [
            'exact' => 'bool',
            'idGame' => 'int',
            'idLanguage' => 'int'
        ];

        $data = ['search' => str_replace(' ', '', $search)];
        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        $data += $this->setUpOptionalParameters($searchData, $optional);

        return $this->get(sprintf('/products/find?%s', http_build_query($data)));
    }
}
