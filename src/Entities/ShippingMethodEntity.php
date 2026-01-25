<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class ShippingMethodEntity extends BaseEntity
{
    private int $idShippingMethod;

    /**
     * Constructor.
     *
     * @param int $idShippingMethod
     */
    public function __construct(int $idShippingMethod)
    {
        parent::__construct();
        $this->idShippingMethod = $idShippingMethod;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<idShippingMethod>' . $this->idShippingMethod . '</idShippingMethod>';
    }
}
