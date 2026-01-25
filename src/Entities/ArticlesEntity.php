<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class ArticlesEntity extends MultipleEntity
{
    protected string $childEntity = ArticleEntity::class;

    public function __construct(ArticleEntity|ArticleBaseEntity|array $entities)
    {
        parent::__construct($entities);
    }

    public function getMe(array $entities): MultipleEntity
    {
        return new self($entities);
    }

    public function getIdsChange(): array
    {
        return array_map(function (ArticleEntity $r) {
            return $r->getIdChange();
        }, $this->entities);
    }

    public function getIdsChangeWithoutError(): array
    {
        return array_map(function (ArticleEntity $r) {
            return $r->getIdChange();
        }, $this->getEntitiesWithoutError());
    }

    public function getEntitiesWithoutError(): array
    {
        return array_filter($this->entities, function (ArticleEntity $r) {
            return !$r->hasError();
        });
    }

    public function getEntityByTried(array $data): array
    {
        return array_filter($this->getEntitiesWithoutError(), function (ArticleEntity $r) use ($data) {
            return $r->isMe($data);
        });
    }
}
