<?php

namespace Pisko\CardMarket\Entities;

abstract class BaseEntity
{
    public function __construct()
    {
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data){
        foreach($data as $key => $value) {
            if (isset($this->$key) && gettype($this->$key) === gettype($value))
                $this->$key = $value;
        }
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public abstract function getPureXML(): string;


    /**
     * Return entity as Request XML
     *
     * @return string
     */
    public function getXML(): string
    {

        return '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<request>' .
            $this->getPureXML() .
            '</request>';
    }
    public function getArray(): array
    {
        return [];
    }
}