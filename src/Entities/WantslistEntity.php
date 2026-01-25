<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class WantslistEntity extends BaseEntity
{
    protected int $idWantslist = 0;

    protected int $idGame = 0;

    protected string $name = '';

    public function __construct(array $data = [])
    {
        parent::__construct();
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setIdGame(int $idGame): void
    {
        $this->idGame = $idGame;
    }

    public function getPureXML(): string
    {
        $xml = '';

        if ($this->idGame > 0) {
            $xml .= '<idGame>' . $this->idGame . '</idGame>';
        }

        if (!empty($this->name)) {
            $xml .= '<name>' . htmlspecialchars($this->name, ENT_XML1, 'UTF-8') . '</name>';
        }

        return $xml;
    }

    public function getArray(): array
    {
        $data = [];

        if ($this->idWantslist > 0) {
            $data['idWantslist'] = $this->idWantslist;
        }

        if ($this->idGame > 0) {
            $data['idGame'] = $this->idGame;
        }

        if (!empty($this->name)) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
