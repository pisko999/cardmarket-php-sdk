<?php

namespace Pisko\CardMarket\Entities;

 abstract class CouponsEntity extends MultipleEntity
{
    protected string $childEntity = ArticleEntity::class;
}