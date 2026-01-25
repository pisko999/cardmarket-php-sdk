<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Helpers\GamesHelper;

class GamesHelperTest extends TestCase
{
    public function testIsGameForValidIds()
    {
        $this->assertTrue(GamesHelper::isGame(GamesHelper::MTG));
        $this->assertTrue(GamesHelper::isGame(GamesHelper::YGO));
        $this->assertTrue(GamesHelper::isGame(GamesHelper::PCG));
    }

    public function testIsGameForInvalidId()
    {
        $this->assertFalse(GamesHelper::isGame(9999));
        $this->assertFalse(GamesHelper::isGame(0));
    }

    public function testGameConstants()
    {
        $this->assertSame(1, GamesHelper::MTG);
        $this->assertSame(3, GamesHelper::YGO);
        $this->assertSame(6, GamesHelper::PCG);
    }

    public function testGamesArray()
    {
        $this->assertIsArray(GamesHelper::GAMES);
        $this->assertContains(1, GamesHelper::GAMES);
        $this->assertContains(6, GamesHelper::GAMES);
    }
}
