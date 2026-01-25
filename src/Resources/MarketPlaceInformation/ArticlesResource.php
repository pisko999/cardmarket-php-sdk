<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class ArticlesResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class ArticlesResource extends HttpCaller
{
    /**
     * Get articles by idProduct.
     *
     * @param int $idProduct
     * @param int $start
     * @param int $maxResults
     * @param array $searchData
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getArticles(
        int $idProduct,
        int $start = 0,
        int $maxResults = 100,
        array $searchData = [],
    ): array {
        $optional = [
            'userType' => 'userType',
            'minUserScore' => 'userScore',
            'idLanguage' => 'language',
            'minCondition' => 'condition',
            'isFoil' => 'bool',
            'isSigned' => 'bool',
            'isAltered' => 'bool',
            'minAvailable' => 'int',
        ];

        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        $data += $this->setUpOptionalParameters($searchData, $optional);

        return $this->get(sprintf('/articles/%d?%s', $idProduct, http_build_query($data)));
    }

    /**
     * Get articles by specified user.
     *
     * @param int $idUser
     * @param int $start
     * @param int $maxResults
     * @param array $searchData
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getArticlesByUser(
        int $idUser,
        int $start = 0,
        int $maxResults = 100,
        array $searchData = [],
    ): array {
        $optional = [
            'idGame' => 'game',
            'idLanguage' => 'language',
            'minCondition' => 'condition',
            'rarity' => 'rarity',
            'isFoil' => 'bool',
            'isSigned' => 'bool',
            'isAltered' => 'bool',
            'isReverseHolo' => 'bool',
            'isFirstEd' => 'bool',
            'isFullArt' => 'bool',
            'isUberRare' => 'bool',
            'isWithDie' => 'bool',
            'isInPackage' => 'bool',
            'expansionName' => 'string',
            'idWantslist' => 'int',
            'name' => 'string',
            'minPrice' => 'float',
            'maxPrice' => 'float',
            'comments' => 'string',
            'minAvailable' => 'int',
            'sort' => 'sort',
        ];

        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        $data += $this->setUpOptionalParameters($searchData, $optional);

        return $this->get(sprintf('/users/%d/articles?%s', $idUser, http_build_query($data)));
    }
}
