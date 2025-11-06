<?php

namespace Pisko\CardMarket\Entities;

use phpDocumentor\Reflection\Types\ClassString;

abstract class MultipleEntity extends BaseEntity
{
    protected string $childEntity;
    protected array $entities = [];


    /**
     * Necessary to pass as array of elements
     *
     * @param array $entities
     */
    public function __construct(BaseEntity|array $entities)
    {
        if ($entities instanceof BaseEntity) {
            $this->entities[] = $entities;
        } elseif (is_array($entities) && reset($entities) instanceof BaseEntity) {
            $this->entities = $entities;
        } else {
            foreach ($entities as $entity) {
                $this->entities[] = new $this->childEntity($entity);
            }
        }
    }


    /**
     * Add entity
     *
     * @param BaseEntity $entity
     * @return void
     */
    public function add(BaseEntity $entity): void {
        if ($entity instanceof $this->childEntity)
            $this->entities[] = $entity;
    }


    /**
     * Parse entity and add
     *
     * @param mixed $entity
     * @return void
     */
    public function parseAdd(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->add(new $this->childEntity($entity));
        }
    }


    public function getCount(): int {
        return count($this->entities);
    }

    public function get(int $id): BaseEntity|null{
        return $this->entities[$id] ?? null;
    }


    public function free(): void {
        $this->entities = [];
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        $ret = '';
        foreach ($this->entities as $entity) {
            $ret .= $entity->getPureXml();
        }
        return $ret;
    }

    /**
     * Return entity as Request XML
     *
     * @return string
     */
    public function getXML(): string
    {
        $ret = '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<request>';
        $ret .= $this->getAditionalXML();
        foreach ($this->entities as $entity) {
            $ret .= $entity->getPureXML();
        }
        $ret .= '</request>';
        return $ret;
    }

    public function getArray(): array{
        $array = [];
        foreach($this->entities as $entity) {
            $array[] = $entity->getArray();
        }
        return $array;
    }

    public function getBatch(): self
    {
        return $this->getMe(array_splice($this->entities, 0, 100));
    }

    protected function getAditionalXML(): string
    {
        return '';
    }

    /**
     * @return class-string<BaseEntity>
     */
    public function getChildEntityClassname(): string
    {
        if (empty($this->childEntity)) {
            throw new \LogicException('Child entity class name is not set.');
        }
        if (!class_exists($this->childEntity)) {
            throw new \LogicException('Child entity class does not exist: ' . $this->childEntity);
        }
        return $this->childEntity::class;
    }

    public abstract function getMe(array $entities): self;
}