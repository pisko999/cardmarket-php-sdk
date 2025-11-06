<?php

namespace Pisko\CardMarket\Entities;

class ShippingMethodEntity extends BaseEntity
{
    private int $idShippingMethod;


    /**
     * Constructor
     *
     * @param string $coupon
     */
    public function __construct(int $idShippingMethod)
    {
        $this->idShippingMethod = $idShippingMethod;
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<idShippingMethod>' . $this->idShippingMethod . '</idShippingMethod>';
    }

}