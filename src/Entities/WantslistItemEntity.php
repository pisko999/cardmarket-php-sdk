<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class WantslistItemEntity extends BaseEntity
{
    protected int $idWant = 0;

    protected int $idProduct = 0;

    protected int $idMetaproduct = 0;

    protected int $count = 1;

    protected int $wishPrice = 0;

    protected int $idLanguage = 1;

    protected string $minCondition = 'EX';

    protected bool $isFoil = false;

    protected bool $isSigned = false;

    protected bool $isAltered = false;

    protected bool $isFirstEd = false;

    public function __construct(array $data)
    {
        parent::__construct();
        $this->hydrate($data);
    }

    public function getPureXML(): string
    {
        $xml = '<want>';

        if ($this->idWant > 0) {
            $xml .= '<idWant>' . $this->idWant . '</idWant>';
        }

        if ($this->idProduct > 0) {
            $xml .= '<idProduct>' . $this->idProduct . '</idProduct>';
        } elseif ($this->idMetaproduct > 0) {
            $xml .= '<idMetaproduct>' . $this->idMetaproduct . '</idMetaproduct>';
        }

        if ($this->count > 0) {
            $xml .= '<count>' . $this->count . '</count>';
        }

        if ($this->wishPrice > 0) {
            $xml .= '<wishPrice>' . $this->wishPrice . '</wishPrice>';
        }

        if ($this->idLanguage > 0) {
            $xml .= '<idLanguage>' . $this->idLanguage . '</idLanguage>';
        }

        if (!empty($this->minCondition)) {
            $xml .= '<minCondition>' . $this->minCondition . '</minCondition>';
        }

        if ($this->isFoil) {
            $xml .= '<isFoil>true</isFoil>';
        }

        if ($this->isSigned) {
            $xml .= '<isSigned>true</isSigned>';
        }

        if ($this->isAltered) {
            $xml .= '<isAltered>true</isAltered>';
        }

        if ($this->isFirstEd) {
            $xml .= '<isFirstEd>true</isFirstEd>';
        }

        $xml .= '</want>';

        return $xml;
    }

    public function getArray(): array
    {
        $data = [];

        if ($this->idWant > 0) {
            $data['idWant'] = $this->idWant;
        }

        if ($this->idProduct > 0) {
            $data['idProduct'] = $this->idProduct;
        }

        if ($this->idMetaproduct > 0) {
            $data['idMetaproduct'] = $this->idMetaproduct;
        }

        if ($this->count > 0) {
            $data['count'] = $this->count;
        }

        if ($this->wishPrice > 0) {
            $data['wishPrice'] = $this->wishPrice;
        }

        if ($this->idLanguage > 0) {
            $data['idLanguage'] = $this->idLanguage;
        }

        if (!empty($this->minCondition)) {
            $data['minCondition'] = $this->minCondition;
        }

        if ($this->isFoil) {
            $data['isFoil'] = $this->isFoil;
        }

        if ($this->isSigned) {
            $data['isSigned'] = $this->isSigned;
        }

        if ($this->isAltered) {
            $data['isAltered'] = $this->isAltered;
        }

        if ($this->isFirstEd) {
            $data['isFirstEd'] = $this->isFirstEd;
        }

        return $data;
    }
}
