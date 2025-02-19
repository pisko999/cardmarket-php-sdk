<?php

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Entities\ArticlesEntity;
use Pisko\CardMarket\Entities\MultipleEntity;
use Pisko\CardMarket\Enums\HttpMethods;
use Pisko\CardMarket\Resources\ModelMultipleResource;

class AddArticleStockResource extends ModelMultipleResource
{
    protected MultipleEntity $entity;
    protected HttpMethods $httpMethod = HttpMethods::post;
    protected string $url = '/stock';
    protected string $className = ArticlesEntity::class;
}