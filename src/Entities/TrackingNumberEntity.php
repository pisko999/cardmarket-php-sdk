<?php

namespace Pisko\CardMarket\Entities;

class TrackingNumberEntity extends BaseEntity
{
    private string $trackingNumber;


    /**
     * Constructor
     *
     * @param string $trackingNumber
     */
    public function __construct(string $trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<trackingNumber>' . $this->trackingNumber . '</trackingNumber>';
    }

}