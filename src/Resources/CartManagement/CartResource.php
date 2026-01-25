<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\CartManagement;

use Pisko\CardMarket\Entities\CartAddressEntity;
use Pisko\CardMarket\Entities\CartArticlesEntity;
use Pisko\CardMarket\Entities\MultipleEntity;
use Pisko\CardMarket\Entities\ShippingMethodEntity;
use Pisko\CardMarket\Enums\HttpMethods;
use Pisko\CardMarket\Resources\ModelMultipleResource;

/**
 * Class CartResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class CartResource extends ModelMultipleResource
{
    protected MultipleEntity $entity;

    protected HttpMethods $httpMethod = HttpMethods::put;

    protected string $url = '/shoppingcart';

    protected string $className = CartArticlesEntity::class;

    /**
     * set the action for the cart operation.
     *
     * @param string $action
     *
     * @return bool
     */
    public function setAction(string $action): bool
    {
        return ($this->entity instanceof CartArticlesEntity) ? $this->entity->setAction($action) : false;
    }

    public function getAction(): ?string
    {
        return ($this->entity instanceof CartArticlesEntity) ? $this->entity->getAction() : null;
    }

    public function getEntity(): MultipleEntity
    {
        return $this->entity;
    }

    public function getCart(): array
    {
        return $this->get('/shoppingcart');
    }

    public function emptyCart(): array
    {
        return $this->delete('/shoppingcart');
    }

    public function getShippingMethods(int $idReservation): array
    {
        return $this->get(sprintf('/shoppingcart/shippingmethod/%d', $idReservation));
    }

    public function setShippingMethod(int $idReservation, int $idShippingMethod): array
    {
        $shippingMethod = new ShippingMethodEntity($idShippingMethod);

        return $this->put(sprintf('/shoppingcart/shippingmethod/%d', $idReservation), $shippingMethod);
    }

    public function setCartAddress(CartAddressEntity $cartAddress): array
    {
        return $this->put('/shoppingcart/shippingaddress', $cartAddress);
    }

    public function checkout(): array
    {
        return $this->put('/shoppingcart/checkout');
    }
}
