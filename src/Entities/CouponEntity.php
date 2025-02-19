<?php

namespace Pisko\CardMarket\Entities;

class CouponEntity extends BaseEntity
{
    private string $coupon;


    /**
     * Constructor
     *
     * @param string $coupon
     */
    public function __construct(string $coupon)
    {
        $this->coupon = $coupon;
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<couponCode>' . $this->coupon . '</couponCode>';
    }

}