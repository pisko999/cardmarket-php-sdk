<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

abstract class BaseEntity
{
    public function __construct()
    {
    }

    /**
     * Hydrate entity with data from array.
     *
     * @param array $data
     *
     * @return void
     */
    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    abstract public function getPureXML(): string;

    /**
     * Return entity as Request XML.
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

    /**
     * Return entity as array.
     *
     * @return array
     */
    public function getArray(): array
    {
        return [];
    }
}
