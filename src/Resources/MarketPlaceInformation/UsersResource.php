<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\MarketPlaceInformation;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class UsersResource
 *
 * @package Pisko\CardMarket\Resources\MarketPlaceInformation
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
     * @return array
     * @throws \Exception
     */
    public function getUserDetails(int|string $user): array
    {
        return $this->get(sprintf('/users/%s', $user));
    }

    /**
     * Find user by name
     *
     * @param string $search
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function findUsers(string $search): array
    {
        $data = ['search' => str_replace(' ', '', $search)];
        return $this->get(sprintf('/users/find?%s', http_build_query($data)));
    }
}
