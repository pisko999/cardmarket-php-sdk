<?php

namespace Pisko\CardMarket\Resources\StockManagement;

use Pisko\CardMarket\Entities\ArticleEntity;
use Pisko\CardMarket\Entities\ArticlesEntity;
use Pisko\CardMarket\Entities\BaseEntity;
use Pisko\CardMarket\Entities\MultipleEntity;
use Pisko\CardMarket\Enums\HttpMethods;
use Pisko\CardMarket\Resources\HttpCaller;
use Pisko\CardMarket\Resources\ModelMultipleResource;

class DeleteArticleStockResource extends ModelMultipleResource
{
    protected MultipleEntity $entity;
    protected HttpMethods $httpMethod = HttpMethods::delete;
    protected string $url = '/stock';
    protected string $className = ArticlesEntity::class;

}