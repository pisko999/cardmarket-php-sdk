<?php

namespace Pisko\CardMarket\Entities;

class CouponsEntity extends MultipleEntity
{
    protected string $childEntity = ArticleEntity::class;
}