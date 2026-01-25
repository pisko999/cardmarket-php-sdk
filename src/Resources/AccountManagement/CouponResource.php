<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\AccountManagement;

use Pisko\CardMarket\Entities\CouponsEntity;
use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class CouponResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class CouponResource extends HttpCaller
{
    /**
     * Redeems one or more coupons.
     *
     * @param string|array $coupons
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function redeemCoupons(string|array $coupons): array
    {
        if (is_string($coupons)) {
            $coupons = [$coupons];
        }
        $coupons = new CouponsEntity($coupons);

        return $this->post('/account/coupon', $coupons);
    }
}
