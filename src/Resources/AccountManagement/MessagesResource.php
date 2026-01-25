<?php

namespace Pisko\CardMarket\Resources\AccountManagement;

use Pisko\CardMarket\Entities\MessageEntity;
use Pisko\CardMarket\Exception\HttpClientException;
use Pisko\CardMarket\Resources\HttpCaller;
use DateTime;
use Symfony\Component\HttpClient\Exception\ClientException;
use function PHPUnit\Framework\isInstanceOf;

/**
 * Class MessagesResource
 *
 * @package Pisko\CardMarket\Resources\AcountManagement
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 * @author Petr Spinar <spinarp@gmail.com>
 */
final class MessagesResource extends HttpCaller
{
    /**
     * Returns the message thread overview of the user.
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMessagesThread(): array
    {
        return $this->get('/account/messages');
    }


    /**
     * Returns a specified message with a specified other user.
     *
     * @param int $idOtherUser
     * @param string $idMessage
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMessageByUser(int $idOtherUser, string $idMessage): array
    {
        return $this->get(sprintf('/account/messages/%d/%s', $idOtherUser, $idMessage));
    }


    /**
     * Returns the message thread with a specified other user.
     *
     * @param int $idOtherUser
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMessagesThreadByUser(int $idOtherUser): array
    {
        return $this->get(sprintf('/account/messages/%d', $idOtherUser));
    }


    /**
     * Creates a new message sent to a specified other user.
     *
     * @param int $idOtherUser
     * @param string $message
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sendMessage(int $idOtherUser, string|MessageEntity $message): array
    {
        if (!$message instanceof MessageEntity) {
            $message = new MessageEntity($message);
        }
        return $this->post(sprintf('/account/messages/%d', $idOtherUser), $message);
    }


    /**
     * Deletes a complete message thread to a specified other user.
     *
     * WARNING: This API endpoint may not actually delete messages - it appears to only
     * hide the thread from the main listing. Use deleteOneMessageByUser() to reliably
     * delete individual messages.
     *
     * @param int $idOtherUser
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function deleteMessagesByUser(int $idOtherUser): array
    {
        return $this->delete(sprintf('/account/messages/%d', $idOtherUser));
    }


    /**
     * Deletes a specified message to a specified other user.
     *
     * @param int $idOtherUser
     * @param string $idMessage
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function deleteOneMessageByUser(int $idOtherUser, string $idMessage): array
    {
        return $this->delete(sprintf('/account/messages/%d/%s', $idOtherUser, $idMessage));
    }


    /**
     * Returns messages matching the specified query parameter values.
     *
     * WARNING: This API endpoint is currently disabled/broken by Cardmarket.
     * Known issues:
     * - Returns HTTP 400 with "This feature is currently unavailable." error
     * - Despite returning an error, it MARKS ALL UNREAD MESSAGES AS READ!
     * - Does not return any message data
     *
     * The method is kept for forward compatibility in case Cardmarket fixes it.
     * DO NOT USE THIS METHOD unless you want to mark all messages as read.
     *
     * @param bool $unread
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Pisko\CardMarket\Exception\HttpClientException May throw with "unavailable" message
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array
     */
    public function findMessages(bool $unread, ?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $data = [];
        if ($unread) {
            $data['unread'] = 'true';
        } else {
            if ($startDate) {
                $data['startDate'] = $startDate->format('Y-m-d');
            }
            if ($endDate) {
                $data['endDate'] = $endDate->format('Y-m-d');
            }
        }
        
        return $this->get(sprintf('/account/messages/find?%s', http_build_query($data)));
    }
}
