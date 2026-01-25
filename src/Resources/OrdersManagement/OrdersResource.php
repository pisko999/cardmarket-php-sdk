<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources\OrdersManagement;

use Pisko\CardMarket\Entities\EvaluationEntity;
use Pisko\CardMarket\Entities\OrderChangeStateEntity;
use Pisko\CardMarket\Entities\TrackingNumberEntity;
use Pisko\CardMarket\Resources\HttpCaller;

/**
 * Class OrdersResource.
 *
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class OrdersResource extends HttpCaller
{
    public const ORDER_SELLER = 'seller';

    public const ORDER_BUYER = 'buyer';

    public const ORDER_STATE_BOUGHT = 'bought';

    public const ORDER_STATE_PAID = 'paid';

    public const ORDER_STATE_SENT = 'sent';

    public const ORDER_STATE_RECEIVED = 'received';

    public const ORDER_STATE_LOST = 'lost';

    public const ORDER_STATE_CANCELLED = 'cancelled';

    /**
     * Retrieve order details by order ID.
     *
     * @param int $orderId
     *
     * @throws \Pisko\CardMarket\Exception\HttpClientException
     *
     * @return array
     */
    public function getOrder(int $orderId): array
    {
        return $this->get(sprintf('/order/%d', $orderId));
    }

    public function changeOrderState(int $orderId, string $action, ?string $reason = null, ?string $relistItems = null): array
    {
        return $this->put(sprintf('/order/%d', $orderId), new OrderChangeStateEntity($action, $reason, $relistItems));
    }

    /**
     * Set the tracking number for an order.
     *
     * @param int $orderId
     * @param string $trackingNumber
     *
     * @return array
     */
    public function setOrderTrackingNumber(int $orderId, string $trackingNumber): array
    {
        return $this->put(sprintf('/order/%d/tracking', $orderId), new TrackingNumberEntity($trackingNumber));
    }

    public function evaluateOrder(
        int $orderId,
        int $evaluationGrade = EvaluationEntity::GRADE_VERY_GOOD,
        int $itemDescription = EvaluationEntity::GRADE_VERY_GOOD,
        int $packaging = EvaluationEntity::GRADE_VERY_GOOD,
        string $comment = '',
        array $complaints = [],
    ): array {
        $evaluation = new EvaluationEntity($evaluationGrade, $itemDescription, $packaging, $comment, $complaints);

        return $this->post(sprintf('/order/%d/evaluation', $orderId), $evaluation);
    }

    /**
     * Rerieve all filtered orders.
     *
     * @param string $actor
     * @param string $state
     * @param int $start
     *
     * @return array
     */
    public function getOrders(string $actor, string $state, int $start = 1): array
    {
        if (!in_array($actor, [self::ORDER_SELLER, self::ORDER_BUYER])) {
            throw new \InvalidArgumentException(sprintf('Invalid actor "%s".', $actor));
        }
        if (!in_array($state, [
            self::ORDER_STATE_BOUGHT,
            self::ORDER_STATE_PAID,
            self::ORDER_STATE_SENT,
            self::ORDER_STATE_RECEIVED,
            self::ORDER_STATE_LOST,
            self::ORDER_STATE_CANCELLED,
        ])) {
            throw new \InvalidArgumentException(sprintf('Invalid state "%s".', $state));
        }

        return $this->get(sprintf('/orders/%s/%s/%d', $actor, $state, $start));
    }

    /**
     * Returns all send orders for the current seller.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getSentOrders($start = 1): array
    {
        return $this->get(sprintf('/orders/%s/%s/%d', self::ORDER_SELLER, self::ORDER_STATE_SENT, $start));
    }

    /**
     * Returns all received orders for the current seller.
     *
     * @param int $start
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getReceivedOrders(int $start = 1): array
    {
        return $this->get(sprintf('/orders/%s/%s/%d', self::ORDER_SELLER, self::ORDER_STATE_RECEIVED, $start));
    }
}
