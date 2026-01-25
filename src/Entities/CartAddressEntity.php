<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class CartAddressEntity extends BaseEntity
{
    private string $name;

    private string $extra;

    private string $street;

    private string $zip;

    private string $city;

    private string $country;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $extra
     * @param string $street
     * @param string $zip
     * @param string $city
     * @param string $countryCode
     */
    public function __construct(string $name, string $extra, string $street, string $zip, string $city, string $countryCode)
    {
        parent::__construct();
        $this->name = $name;
        $this->extra = $extra;
        $this->street = $street;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $countryCode;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return
        '<name>' . $this->name . '</name>' .
        '<extra>' . $this->extra . '</extra>' .
        '<street>' . $this->street . '</street>' .
        '<zip>' . $this->zip . '</zip>' .
        '<city>' . $this->city . '</city>' .
        '<country>' . $this->country . '</country>';
    }
}
