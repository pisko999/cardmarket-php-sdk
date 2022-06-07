<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class MetaproductsResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
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
     * @return array
     * @throws \Exception
     */
    public function getMetaProductDetails(int $idMetaproduct): array
    {
        return $this->get(sprintf('/metaproducts/%d', $idMetaproduct));
    }

    /**
     * Find metaproducts by name
     * not working with spaces :(
     *
     * @param string $search
     * @param array $searchData
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function findMetaProducts(
        string $search,
        array $searchData = []
    ): array
    {
        $data = ['search' => str_replace(' ', '', $search)];

        if (isset($searchData['exact'])) {
            $data['exact'] = $searchData['exact'] ? 'true' : 'false';
        }
        if (isset($searchData['idGame'])) {
            $data['idGame'] = $searchData['idGame'];
        }
        if (isset($searchData['idLanguage'])) {
            $data['idLanguage'] = $searchData['idLanguage'];
        }
        return $this->get(sprintf('/metaproducts/find?%s', http_build_query($data)));
    }
}
