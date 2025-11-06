<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources\AccountManagement;

use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class AccountResource
 *
 * @package Pisko\CardMarket\Resources\AcountManagement
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class AccountResource extends HttpCaller
{
    /**
     * Returns the account details of the authenticated user.
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getAccountInformation(): array
    {
        return $this->get('/account');
    }


    /**
     * Updates the vacation status of the user.
     *
     * @param bool $onVacation
     * @param array $setData
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function setOnVacation(bool $onVacation, array $setData): array
    {
        $optional = [
            'cancelOrders' => 'bool',
            'relistItems' => 'bool'
        ];
        $data['onVacation'] = $onVacation ? 'true' : 'false';

        $data += $this->setUpOptionalParameters($setData, $optional);
        return $this->put(sprintf('/account/vacation?%s', http_build_query($data)));
    }


    /**
     * Updates the display language of the authenticated user.
     *
     * @param int $idDisplayLanguage
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function setDisplayLanguage(int $idDisplayLanguage): array
    {
        $data['idDisplayLanguage'] = $idDisplayLanguage;

        return $this->put(sprintf('/account/language?%s', http_build_query($data)));
    }
}
