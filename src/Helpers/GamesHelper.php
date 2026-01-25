<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Helpers;

/**
 * Helper class for Cardmarket game IDs.
 *
 * Game IDs 4 and 14 are discontinued/removed from the API.
 */
final class GamesHelper
{
    public const MTG = 1;      // Magic: The Gathering

    public const WOW = 2;      // World of Warcraft TCG

    public const YGO = 3;      // Yu-Gi-Oh!

    public const SPOILS = 5;   // The Spoils

    public const PCG = 6;      // Pokémon

    public const FOW = 7;      // Force of Will

    public const CFV = 8;      // Cardfight!! Vanguard

    public const FF = 9;       // Final Fantasy TCG

    public const WS = 10;      // Weiß Schwarz

    public const DGB = 11;     // Dragoborne

    public const MLP = 12;     // My Little Pony

    public const DBS = 13;     // Dragon Ball Super

    public const SWD = 15;     // Star Wars: Destiny

    /**
     * List of all active game IDs.
     */
    public const GAMES = [
        self::MTG,
        self::WOW,
        self::YGO,
        self::SPOILS,
        self::PCG,
        self::FOW,
        self::CFV,
        self::FF,
        self::WS,
        self::DGB,
        self::MLP,
        self::DBS,
        self::SWD,
    ];

    public static function isGame(int $idGame): bool
    {
        return in_array($idGame, self::GAMES, true);
    }
}
