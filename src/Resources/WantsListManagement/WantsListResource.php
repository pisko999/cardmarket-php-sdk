<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\WantsListManagement;

use Pisko\CardMarket\Entities\WantslistEntity;
use Pisko\CardMarket\Entities\WantslistItemsEntity;
use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class WantsListResource.
 *
 *
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class WantsListResource extends HttpCaller
{
    /**
     * Returns all wantslists for the authenticated user.
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getWantsLists(): array
    {
        return $this->get('/wantslist');
    }

    /**
     * Returns a specific wantslist with all items.
     *
     * @param int $idWantslist
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getWantsList(int $idWantslist): array
    {
        return $this->get(sprintf('/wantslist/%d', $idWantslist));
    }

    /**
     * Creates a new wantslist for the authenticated user.
     *
     * @param string $name
     * @param int $idGame
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function createWantsList(string $name, int $idGame): array
    {
        $wantslist = new WantslistEntity([
            'name' => $name,
            'idGame' => $idGame,
        ]);

        return $this->post('/wantslist', $wantslist);
    }

    /**
     * Renames a wantslist.
     *
     * @param int $idWantslist
     * @param string $newName
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function renameWantsList(int $idWantslist, string $newName): array
    {
        $wantslist = new WantslistEntity(['name' => $newName]);

        return $this->put(sprintf('/wantslist/%d', $idWantslist), $wantslist);
    }

    /**
     * Deletes a wantslist and all items on it.
     *
     * @param int $idWantslist
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function deleteWantsList(int $idWantslist): array
    {
        return $this->delete(sprintf('/wantslist/%d', $idWantslist));
    }

    /**
     * Adds items to a wantslist.
     *
     * @param int $idWantslist
     * @param WantslistItemsEntity $items
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function addItemsToWantsList(int $idWantslist, WantslistItemsEntity $items): array
    {
        $items->setAction(WantslistItemsEntity::ACTION_ADDITEM);

        return $this->put(sprintf('/wantslist/%d', $idWantslist), $items);
    }

    /**
     * Edits items in a wantslist.
     *
     * @param int $idWantslist
     * @param WantslistItemsEntity $items
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function editItemsInWantsList(int $idWantslist, WantslistItemsEntity $items): array
    {
        $items->setAction(WantslistItemsEntity::ACTION_EDITITEM);

        return $this->put(sprintf('/wantslist/%d', $idWantslist), $items);
    }

    /**
     * Deletes items from a wantslist.
     *
     * @param int $idWantslist
     * @param WantslistItemsEntity $items
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function deleteItemsFromWantsList(int $idWantslist, WantslistItemsEntity $items): array
    {
        $items->setAction(WantslistItemsEntity::ACTION_DELETEITEM);

        return $this->put(sprintf('/wantslist/%d', $idWantslist), $items);
    }
}
