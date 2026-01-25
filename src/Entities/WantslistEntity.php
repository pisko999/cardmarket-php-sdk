<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class WantslistEntity extends BaseEntity
{
    public const ACTION_EDIT = 'editWantslist';

    protected int $idWantslist = 0;

    protected int $idGame = 0;

    protected string $name = '';

    protected string $action = '';

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

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getPureXML(): string
    {
        // For edit/rename operation - different format without wrapper
        if ($this->action === self::ACTION_EDIT) {
            $xml = '<action>' . $this->action . '</action>';
            if (!empty($this->name)) {
                $xml .= '<name>' . htmlspecialchars($this->name, ENT_XML1, 'UTF-8') . '</name>';
            }

            return $xml;
        }

        // For create operation - with <wantslist> wrapper
        $xml = '<wantslist>';

        if ($this->idGame > 0) {
            $xml .= '<idGame>' . $this->idGame . '</idGame>';
        }

        if (!empty($this->name)) {
            $xml .= '<name>' . htmlspecialchars($this->name, ENT_XML1, 'UTF-8') . '</name>';
        }

        $xml .= '</wantslist>';

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
