<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Exception;

use Pisko\CardMarket\CardMarketException;

final class UnknownErrorException extends \Exception implements CardMarketException
{
}
