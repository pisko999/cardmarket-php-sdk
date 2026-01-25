<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Helpers\UserTypeHelper;

class UserTypeHelperTest extends TestCase
{
    public function testUserTypeConstants()
    {
        $this->assertSame('private', UserTypeHelper::PRIVATE);
        $this->assertSame('commercial', UserTypeHelper::COMMERCIAL);
        $this->assertSame('powerseller', UserTypeHelper::POWERSELLER);
    }

    public function testConstantsAreDifferent()
    {
        $this->assertNotEquals(UserTypeHelper::PRIVATE, UserTypeHelper::COMMERCIAL);
        $this->assertNotEquals(UserTypeHelper::COMMERCIAL, UserTypeHelper::POWERSELLER);
        $this->assertNotEquals(UserTypeHelper::PRIVATE, UserTypeHelper::POWERSELLER);
    }
}
