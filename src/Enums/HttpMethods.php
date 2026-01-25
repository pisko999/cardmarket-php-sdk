<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Enums;

/**
 * HTTP Methods enum for API requests.
 */
enum HttpMethods
{
    case get;
    case post;
    case put;
    case delete;
}
