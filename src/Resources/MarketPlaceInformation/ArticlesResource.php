<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class ArticlesResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class ArticlesResource extends HttpCaller
{
    /**
     * Get articles by idProduct
     *
     * @param int $idProduct
     * @param int $start
     * @param int $maxResults
     * @param array $searchData
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getArticles(
        int $idProduct,
        int $start = 0,
        int $maxResults = 100,
        array $searchData = []
    ): array
    {
        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        if (isset($searchData['userType'])) {
            $data['userType'] = $searchData['userType'];
        }
        if (isset($searchData['minUserScore'])) {
            $data['minUserScore'] = $searchData['minUserScore'];
        }
        if (isset($searchData['idLanguage'])) {
            $data['idLanguage'] = $searchData['idLanguage'];
        }
        if (isset($searchData['minCondition'])) {
            $data['minCondition'] = $searchData['minCondition'];
        }
        if (isset($searchData['isFoil'])) {
            $data['isFoil'] = $searchData['isFoil'];
        }
        if (isset($searchData['isSigned'])) {
            $data['isSigned'] = $searchData['isSigned'];
        }
        if (isset($searchData['isAltered'])) {
            $data['isAltered'] = $searchData['isAltered'];
        }
        if (isset($searchData['minAvailable'])) {
            $data['minAvailable'] = $searchData['minAvailable'];
        }

        return $this->get(sprintf('/articles/%d?%s', $idProduct, http_build_query($data)));
    }

    /**
     * Get articles by specified user
     *
     * @param int $idUser
     * @param int $start
     * @param int $maxResults
     * @param array $searchData
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getArticlesByUser(
        int $idUser,
        int $start = 0,
        int $maxResults = 100,
        array $searchData = []
    ): array
    {
        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        if (isset($searchData['name'])) {
            $data['name'] = $searchData['name'];
        }
        if (isset($searchData['expansionName'])) {
            $data['expansionName'] = $searchData['expansionName'];
        }
        if (isset($searchData['idGame'])) {
            $data['idGame'] = $searchData['idGame'];
        }
        if (isset($searchData['idLanguage'])) {
            $data['idLanguage'] = $searchData['idLanguage'];
        }
        if (isset($searchData['minCondition'])) {
            $data['minCondition'] = $searchData['minCondition'];
        }
        if (isset($searchData['rarity'])) {
            $data['rarity'] = $searchData['rarity'];
        }
        if (isset($searchData['isFoil'])) {
            $data['isFoil'] = $searchData['isFoil'] ? 'true' : 'false';
        }
        if (isset($searchData['isSigned'])) {
            $data['isSigned'] = $searchData['isSigned'] ? 'true' : 'false';
        }
        if (isset($searchData['isAltered'])) {
            $data['isAltered'] = $searchData['isAltered'] ? 'true' : 'false';
        }
        if (isset($searchData['isReverseHolo'])) {
            $data['isReverseHolo'] = $searchData['isReverseHolo'] ? 'true' : 'false';
        }
        if (isset($searchData['isPlayset'])) {
            $data['isPlayset'] = $searchData['isPlayset'] ? 'true' : 'false';
        }
        if (isset($searchData['isFirstEd'])) {
            $data['isFirstEd'] = $searchData['isFirstEd'] ? 'true' : 'false';
        }
        if (isset($searchData['isFullArt'])) {
            $data['isFullArt'] = $searchData['isFullArt'] ? 'true' : 'false';
        }
        if (isset($searchData['isUberRare'])) {
            $data['isUberRare'] = $searchData['isUberRare'] ? 'true' : 'false';
        }
        if (isset($searchData['isWithDie'])) {
            $data['isWithDie'] = $searchData['isWithDie'] ? 'true' : 'false';
        }
        if (isset($searchData['isInPackage'])) {
            $data['isInPackage'] = $searchData['isInPackage'] ? 'true' : 'false';
        }
        if (isset($searchData['idWantslist'])) {
            $data['idWantslist'] = $searchData['idWantslist'];
        }
        if (isset($searchData['minPrice'])) {
            $data['minPrice'] = $searchData['minPrice'];
        }
        if (isset($searchData['maxPrice'])) {
            $data['maxPrice'] = $searchData['maxPrice'];
        }
        if (isset($searchData['comments'])) {
            $data['comments'] = $searchData['comments'];
        }
        if (isset($searchData['minAvailable'])) {
            $data['minAvailable'] = $searchData['minAvailable'];
        }
        if (isset($searchData['sort'])) {
            $data['sort'] = $searchData['sort'];
        }

        return $this->get(sprintf('/users/%d/articles?%s', $idUser, http_build_query($data)));
    }
}
