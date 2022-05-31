<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Helpers;

final class GamesHelper
{
    const MTG = 1;
    const WOW = 2;
    const YGO = 3;
    const SPOILS = 5;
    const PCG = 6;
    const FOW = 7;
    const CFV = 8;
    const FF = 9;
    const WS = 10;
    const DGB = 11;
    const MLP = 12;
    const DBS = 13;
    const SWD = 15;

    const GAMES = [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15];

    public static function isGame(int $idGame): bool
    {
        return in_array($idGame, self::GAMES);
    }
}