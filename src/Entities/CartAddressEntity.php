<?php

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
     * Constructor
     *
     * @param string $coupon
     */
    public function __construct(string $name, string $extra, string $street, string $zip, string $city, string $countryCode)
    {
        $this->name = $name;
        $this->extra = $extra;
        $this->street = $street;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $countryCode;
    }


    /**
     * Return entity as XML
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