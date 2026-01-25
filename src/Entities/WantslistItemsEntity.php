<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class WantslistItemsEntity extends MultipleEntity
{
    protected string $childEntity = WantslistItemEntity::class;

    private string $action = '';

    public const ACTION_ADDITEM = 'addItem';

    public const ACTION_EDITITEM = 'editItem';

    public const ACTION_DELETEITEM = 'deleteItem';

    public function __construct(BaseEntity|array $entities)
    {
        parent::__construct($entities);
    }

    public function setAction(string $action): bool
    {
        if (!in_array($action, [self::ACTION_ADDITEM, self::ACTION_EDITITEM, self::ACTION_DELETEITEM])) {
            throw new \InvalidArgumentException('Invalid action provided. Use WantslistItemsEntity::ACTION_* constants.');
        }

        $this->action = $action;

        return true;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getAditionalXML(): string
    {
        if (empty($this->action)) {
            throw new \LogicException('Action must be set before generating XML.');
        }

        return '<action>' . $this->action . '</action>';
    }

    public function getMe(array $entities): MultipleEntity
    {
        $me = new self($entities);
        $me->action = $this->action;

        return $me;
    }
}
