<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Exception;

use Pisko\CardMarket\CardMarketException;

/**
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class HttpClientNotConfiguredException extends \RuntimeException implements CardMarketException
{
    public function __construct()
    {
        parent::__construct('You need to provide "access_secret", "access_token", "app_secret" and "app_token" to create a correct HttpClient.');
    }
}
