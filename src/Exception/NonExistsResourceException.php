<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Exception;

use Pisko\CardMarket\CardMarketException;

/**
 * Exception thrown when a resource class does not exist.
 */
final class NonExistsResourceException extends \Exception implements CardMarketException
{
    public function __construct(string $message = 'Resource does not exist.')
    {
        parent::__construct($message);
    }
}
