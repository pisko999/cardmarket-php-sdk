<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class MessageEntity extends BaseEntity
{
    private string $message;

    /**
     * Constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct();
        $this->message = $message;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<message>' . htmlspecialchars($this->message, ENT_XML1, 'UTF-8') . '</message>';
    }
}
