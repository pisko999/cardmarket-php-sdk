<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class TrackingNumberEntity extends BaseEntity
{
    private string $trackingNumber;

    /**
     * Constructor.
     *
     * @param string $trackingNumber
     */
    public function __construct(string $trackingNumber)
    {
        parent::__construct();
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<trackingNumber>' . htmlspecialchars($this->trackingNumber, ENT_XML1, 'UTF-8') . '</trackingNumber>';
    }
}
