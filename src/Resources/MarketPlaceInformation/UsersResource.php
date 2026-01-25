<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class UsersResource.
 *
 *
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class UsersResource extends HttpCaller
{
    /**
     * Returns a user specified by its ID or EXACT NAME.
     *
     * @param int|string $user
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getUserDetails(int|string $user): array
    {
        return $this->get(sprintf('/users/%s', $user));
    }

    /**
     * Find user by name.
     *
     * @param string $search
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function findUsers(string $search): array
    {
        $data = ['search' => str_replace(' ', '', $search)];

        return $this->get(sprintf('/users/find?%s', http_build_query($data)));
    }

    /**
     * Request export of user offers.
     *
     * @param int $idUser
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function requestExportUserOffersById(int $idUser): array
    {
        return $this->post(sprintf('/exports/userOffers/%d', $idUser));
    }

    /**
     * Get requested user offers by ID.
     *
     * @param int $idUser
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getRequestedUserOffersById(int $idUser): array
    {
        return $this->get(sprintf('/exports/userOffers/%d', $idUser));
    }

    /**
     * Get export user offers list.
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function getExportUserOffersList(): array
    {
        return $this->get('/exports/userOffers');
    }
}
