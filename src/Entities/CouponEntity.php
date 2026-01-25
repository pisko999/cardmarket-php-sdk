<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class CouponEntity extends BaseEntity
{
    private string $coupon;

    /**
     * Constructor.
     *
     * @param string $coupon
     */
    public function __construct(string $coupon)
    {
        parent::__construct();
        $this->coupon = $coupon;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<couponCode>' . htmlspecialchars($this->coupon, ENT_XML1, 'UTF-8') . '</couponCode>';
    }
}
