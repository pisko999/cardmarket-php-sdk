<?php

namespace Pisko\CardMarket\Resources;

use Pisko\CardMarket\Entities\MultipleEntity;
use Pisko\CardMarket\Enums\HttpMethods;
use Pisko\CardMarket\HttpClient\HttpClientCreator;
use Pisko\CardMarket\Resources\HttpCaller;

abstract class ModelMultipleResource extends HttpCaller
{
    protected MultipleEntity $entity;
    protected HttpMethods $httpMethod;
    protected string $url;
    protected string $className;

    public function __construct(HttpClientCreator $httpClientCreator)
    {
        parent::__construct($httpClientCreator);
    }

    public function add(array $data, bool $async = false): array
    {
        if(isset($this->entity))
        {
            $this->entity->parseAdd($data);
        } else {
            $this->entity = new $this->className($data);
        }

        if ($this->entity->getCount() >= 100 || !$async)
        {
            return $this->Send();
        }
        return ['justAdded' => true];
    }

    public function send(): array
    {
        $ret = [];
        if(isset($this->entity)) {
            while ($this->entity->getCount() > 0) {
                $batch = $this->entity->getBatch();

                $ret[] = [
                    'request' => $batch,
                    'response' =>
                        match ($this->httpMethod) {
                            HttpMethods::get => $this->get($this->url, $batch),
                            HttpMethods::post => $this->post($this->url, $batch),
                            HttpMethods::put => $this->put($this->url, $batch),
                            HttpMethods::delete => $this->delete($this->url, $batch),
                        },
                ];
            }
        }

        return $ret;
    }
}