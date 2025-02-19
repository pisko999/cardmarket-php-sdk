<?php

namespace Pisko\CardMarket\Entities;

class MessageEntity extends BaseEntity
{
    private string $message;


    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<message>' . $this->message . '</message>';
    }

}