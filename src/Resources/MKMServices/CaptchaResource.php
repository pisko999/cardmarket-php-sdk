<?php

namespace Pisko\CardMarket\Resources\MKMServices;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class CaptchaResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class CaptchaResource extends HttpCaller
{
    /**
     * Generates a new captcha.
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function generateCaptcha(): array
    {
        return $this->get('/captcha');
    }
}
