<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class ExpansionsResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class ExpansionsResource extends HttpCaller
{
    /**
     * Returns all expansions with single cards for the specified game.
     *
     * @param int $gameId
     *
     * @return array
     * @throws \Exception
     */
    public function getExpansionsListByGame(int $gameId): array
    {
        return $this->get(sprintf('/games/%d/expansions', $gameId));
    }

    /**
     * Returns all single cards for the specified expansion.
     *
     * @param int $expansionId
     *
     * @return array
     * @throws \Exception
     */
    public function getCardsListByExpansion(int $expansionId): array
    {
        return $this->get(sprintf('/expansions/%d/singles', $expansionId));
    }
}
