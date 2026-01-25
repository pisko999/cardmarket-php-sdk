<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Entities\ArticlesEntity;
use Pisko\CardMarket\Entities\MultipleEntity;
use Pisko\CardMarket\Enums\HttpMethods;
use Pisko\CardMarket\Resources\ModelMultipleResource;

class DeleteArticleStockResource extends ModelMultipleResource
{
    protected MultipleEntity $entity;

    protected HttpMethods $httpMethod = HttpMethods::delete;

    protected string $url = '/stock';

    protected string $className = ArticlesEntity::class;
}
