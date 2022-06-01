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
     * @param string|null $userType
     * @param int|null $minUserScore
     * @param int|null $idLanguage
     * @param string|null $minCondition
     * @param bool|null $isFoil
     * @param bool|null $isSigned
     * @param bool|null $isAltered
     * @param int|null $minAvailable
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
        ?string $userType = null,
        ?int $minUserScore = null,
        ?int $idLanguage = null,
        ?string $minCondition = null,
        ?bool $isFoil = null,
        ?bool $isSigned = null,
        ?bool $isAltered = null,
        ?int $minAvailable = null
    ): array
    {
        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        if ($userType !== null) {
            $data['userType'] = $userType;
        }
        if ($minUserScore !== null) {
            $data['minUserScore'] = $minUserScore;
        }
        if ($idLanguage !== null) {
            $data['idLanguage'] = $idLanguage;
        }
        if ($minCondition !== null) {
            $data['minCondition'] = $minCondition;
        }
        if ($isFoil !== null) {
            $data['isFoil'] = $isFoil;
        }
        if ($isSigned !== null) {
            $data['isSigned'] = $isSigned;
        }
        if ($isAltered !== null) {
            $data['isAltered'] = $isAltered;
        }
        if ($minAvailable !== null) {
            $data['minAvailable'] = $minAvailable;
        }

        return $this->get(sprintf('/articles/%d?%s', $idProduct, http_build_query($data)));
    }

    /**
     * Get articles by specified user
     *
     * @param int $idUser
     * @param int $start
     * @param int $maxResults
     * @param int|null $idGame
     * @param int|null $idLanguage
     * @param string|null $minCondition
     * @param string|null $rarity
     * @param bool|null $isFoil
     * @param bool|null $isSigned
     * @param bool|null $isAltered
     * @param bool|null $isReverseHolo
     * @param bool|null $isPlayset
     * @param bool|null $isFirstEd
     * @param bool|null $isFullArt
     * @param bool|null $isUberRare
     * @param bool|null $isWithDie
     * @param bool|null $isInPackage
     * @param string|null $expansionName
     * @param int|null $idWantslist
     * @param string|null $name
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @param string|null $comments
     * @param int|null $minAvailable
     * @param string|null $sort
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
        ?string $name  = null,
        ?string $expansionName = null,
        ?int $idGame = null,
        ?int $idLanguage = null,
        ?string $minCondition = null,
        ?string $rarity = null,
        ?bool $isFoil = null,
        ?bool $isSigned = null,
        ?bool $isAltered = null,
        ?bool $isReverseHolo = null,
        ?bool $isPlayset = null,
        ?bool $isFirstEd = null,
        ?bool $isFullArt = null,
        ?bool $isUberRare = null,
        ?bool $isWithDie = null,
        ?bool $isInPackage = null,
        ?int $idWantslist = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $comments = null,
        ?int $minAvailable = null,
        ?string $sort = null
    ): array
    {
        $data['start'] = $start;
        $data['maxResults'] = $maxResults;

        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($expansionName !== null) {
            $data['expansionName'] = $expansionName;
        }
        if ($idGame !== null) {
            $data['idGame'] = $idGame;
        }
        if ($idLanguage !== null) {
            $data['idLanguage'] = $idLanguage;
        }
        if ($minCondition !== null) {
            $data['minCondition'] = $minCondition;
        }
        if ($rarity !== null) {
            $data['rarity'] = $rarity;
        }
        if ($isFoil !== null) {
            $data['isFoil'] = $isFoil ? 'true' : 'false';
        }
        if ($isSigned !== null) {
            $data['isSigned'] = $isSigned ? 'true' : 'false';
        }
        if ($isAltered !== null) {
            $data['isAltered'] = $isAltered ? 'true' : 'false';
        }
        if ($isReverseHolo !== null) {
            $data['isReverseHolo'] = $isReverseHolo ? 'true' : 'false';
        }
        if ($isPlayset !== null) {
            $data['isPlayset'] = $isPlayset ? 'true' : 'false';
        }
        if ($isFirstEd !== null) {
            $data['isFirstEd'] = $isFirstEd ? 'true' : 'false';
        }
        if ($isFullArt !== null) {
            $data['isFullArt'] = $isFullArt ? 'true' : 'false';
        }
        if ($isUberRare !== null) {
            $data['isUberRare'] = $isUberRare ? 'true' : 'false';
        }
        if ($isWithDie !== null) {
            $data['isWithDie'] = $isWithDie ? 'true' : 'false';
        }
        if ($isInPackage !== null) {
            $data['isInPackage'] = $isInPackage ? 'true' : 'false';
        }
        if ($idWantslist !== null) {
            $data['idWantslist'] = $idWantslist;
        }
        if ($minPrice !== null) {
            $data['minPrice'] = $minPrice;
        }
        if ($maxPrice !== null) {
            $data['maxPrice'] = $maxPrice;
        }
        if ($comments !== null) {
            $data['comments'] = $comments;
        }
        if ($minAvailable !== null) {
            $data['minAvailable'] = $minAvailable;
        }
        if ($sort !== null) {
            $data['sort'] = $sort;
        }

        return $this->get(sprintf('/users/%d/articles?%s', $idUser, http_build_query($data)));
    }
}
