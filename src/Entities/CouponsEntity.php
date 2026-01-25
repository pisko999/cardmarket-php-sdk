<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class CouponsEntity extends MultipleEntity
{
    protected string $childEntity = CouponEntity::class;

    public function __construct(array $entities)
    {
        parent::__construct($entities);
    }

    public function getMe(array $entities): MultipleEntity
    {
        return new self($entities);
    }
}
