<?php

namespace Pisko\CardMarket\Entities;

class CartArticlesEntity extends MultipleEntity
{
    protected string $childEntity = ArticleBaseEntity::class;

    private string $action = '';

    public const ACTION_ADD = 'add';
    public const ACTION_REMOVE = 'remove';

    public function __construct(BaseEntity|array $entities)
    {
        parent::__construct($entities);
    }

    public function setAction(string $action): bool
    {
        // Validate action
        if (!in_array($action, [self::ACTION_ADD, self::ACTION_REMOVE])) {
            throw new \InvalidArgumentException('Invalid action provided. Use CartArticlesEntity::ACTION_ADD or CartArticlesEntity::ACTION_REMOVE.');
        }

        // Set action
        $this->action = $action;
        return true;
    }


    /**
     * Return entity as Request XML
     *
     * @return string
     */
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
        $me->action = $this->action; // Preserve action for the new instance
        return $me;
    }

    public function getIdsChange(): array
    {
        return array_map(function(ArticleEntity $r){return $r->getIdChange();},$this->entities);
    }

    public function getIdsChangeWithoutError(): array
    {
        return array_map(function(ArticleEntity $r){return $r->getIdChange();},$this->getEntitiesWithoutError());
    }

    public function getEntitiesWithoutError(): array
    {
        return array_filter($this->entities, function(ArticleEntity $r) {return !$r->hasError();});
    }

    public function getEntityByTried(array $data): array
    {
        return array_filter($this->getEntitiesWithoutError(), function(ArticleEntity $r) use ($data) {
            return $r->isMe($data);
        });
    }
}