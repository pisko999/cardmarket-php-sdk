<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Exception;

use Pisko\CardMarket\CardMarketException;

/**
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class HttpServerException extends \RuntimeException implements CardMarketException
{
    public function __construct(
        int $statusCode
    ) {
        parent::__construct(sprintf('An unexpected error occurred on Cardmarket servers. Status code %d.', $statusCode), 0, null);
    }
}
