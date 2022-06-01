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
     * @param bool|null $exact
     * @param int|null $idGame
     * @param int|null $idLanguage
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function findMetaProducts(
        string $search,
        ?bool $exact = null,
        ?int $idGame = null,
        ?int $idLanguage = null
    ): array
    {
        $data = ['search' => str_replace(' ', '', $search)];

        if ($exact !== null) {
            $data['exact'] = $exact ? 'true' : 'false';
        }
        if ($idGame !== null) {
            $data['idGame'] = $idGame;
        }
        if ($idLanguage !== null) {
            $data['idLanguage'] = $idLanguage;
        }
        return $this->get(sprintf('/metaproducts/find?%s', http_build_query($data)));
    }
}
