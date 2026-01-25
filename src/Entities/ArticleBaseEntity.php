<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class ArticleBaseEntity extends BaseEntity
{
    protected int $idArticle = 0;

    protected int $amount = 0;

    public function __construct(array $data)
    {
        parent::__construct();
        $this->hydrate($data);
    }

    public function getPureXML(): string
    {
        return '<article>' .
            '<idArticle>' . $this->idArticle . '</idArticle>' .
            '<amount>' . $this->amount . '</amount>' .
            '</article>';
    }

    public function getArray(): array
    {
        return [
            'idArticle' => $this->idArticle,
            'amount' => $this->amount,
        ];
    }
}
