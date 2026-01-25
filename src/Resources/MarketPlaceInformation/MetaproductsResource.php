<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class MetaproductsResource.
 *
 *
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class MetaproductsResource extends HttpCaller
{
    /**
     * Returns a metaproduct specified by its ID.
     *
     * @param int $idMetaproduct
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getMetaProductDetails(int $idMetaproduct): array
    {
        return $this->get(sprintf('/metaproducts/%d', $idMetaproduct));
    }

    /**
     * Find metaproducts by name
     * not working with spaces :(.
     *
     * @param string $search
     * @param array $searchData
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function findMetaProducts(
        string $search,
        array $searchData = [],
    ): array {
        $optional = [
            'exact' => 'bool',
            'idGame' => 'int',
            'idLanguage' => 'int',
        ];

        $data = ['search' => isset($searchData['exact']) && $searchData['exact'] ? $search : str_replace(' ', '', $search)];

        $data += $this->setUpOptionalParameters($searchData, $optional);

        return $this->get(sprintf('/metaproducts/find?%s', http_build_query($data)));
    }
}
