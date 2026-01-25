<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class ArticleEntity extends ArticleBaseEntity
{
    protected int $idChange = 0;

    protected int $idProduct = 0;

    protected int $idLanguage = 1;

    protected string $comments = '';

    protected int $count = 0;

    protected float $price = 0;

    protected string $condition = 'NM';

    protected bool $isFoil = false;

    protected bool $isSigned = false;

    protected bool $isAltered = false;

    protected bool $error = false;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getIdChange(): int
    {
        return $this->idChange;
    }

    public function hasError(): bool
    {
        return $this->error;
    }

    public function setError(): void
    {
        $this->error = true;
    }

    public function isMe(array $data): bool
    {
        foreach ($data as $key => $value) {
            if ($value == 0) { // non-filled value cant be found on me
                continue;
            }
            if (!isset($this->$key) || $this->$key != $value) {
                return false;
            }
        }

        return true;
    }

    public function getPureXML(): string
    {
        $id = $this->idProduct != 0 ?
            '<idProduct>' . $this->idProduct . '</idProduct>' :
            '<idArticle>' . $this->idArticle . '</idArticle>';

        $xml = '<article>' .
            $id .
            '<idLanguage>' . $this->idLanguage . '</idLanguage>' .
            '<comments>' . $this->comments . '</comments>' .
            '<count>' . $this->count . '</count>' .
            '<price>' . $this->price . '</price>' .
            '<condition>' . $this->condition . '</condition>';
        if ($this->isFoil) {
            $xml .= '<isFoil>true</isFoil>';
        }
        if ($this->isSigned) {
            $xml .= '<isSigned>true</isSigned>';
        }
        if ($this->isAltered) {
            $xml .= '<isAltered>true</isAltered>';
        }
        $xml .= '</article>';

        return $xml;
    }
}
