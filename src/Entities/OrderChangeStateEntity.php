<?php

namespace Pisko\CardMarket\Entities;

class OrderChangeStateEntity extends BaseEntity
{
    private string $action;
    private ?string $reason = null;
    private ?string $relistItems = null;

    // can be performed by the seller, if the current state is paid
    public const STATE_CHANGE_SEND = "send";
    // can be performed by the buyer, if the current state is sent
    public const STATE_CHANGE_CONFIRM_RECEPTION = "confirmReception";
    // can be performed by the seller, if the current state is bought for more than 7 days; can be performed by the buyer, if the current state is paid for more than 7 days
    public const STATE_CHANGE_CANCEL = "cancel";
    // can be performed by both, if the state is not yet sent, the additional key reason is required; if the seller requests cancellation, an optional key relistItems can be provided to indicate, if the articles of the order should be relisted after the cancellation request was accepted by the buyer
    public const STATE_CHANGE_REQUEST_CANCELLATION = "requestCancellation";
    //can be performed by both (but must be opposing actor), if the state is cancellationRequested; if the seller accepts the cancellation request, an optional key relistItems can be provided to indicate, if the articles of the order should be relisted thereafter
    public const STATE_CHANGE_ACCEPT_CANCELLATION = "acceptCancellation";


    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct(string $action, ?string $reason = null, ?string $relistItems = null)
    {

        if (!in_array($action, [
            self::STATE_CHANGE_SEND,
            self::STATE_CHANGE_CONFIRM_RECEPTION,
            self::STATE_CHANGE_CANCEL,
            self::STATE_CHANGE_REQUEST_CANCELLATION,
            self::STATE_CHANGE_ACCEPT_CANCELLATION
        ])) {
            throw new \InvalidArgumentException(sprintf('Invalid action "%s" for changing order state.', $action));
        }
        $this->action = $action;
        $this->reason = $reason;
        $this->relistItems = $relistItems;
    }


    /**
     * Return entity as XML
     *
     * @return string
     */
    public function getPureXML(): string
    {
        return '<action>' . $this->action . '</action>' .
            ($this->reason !== null ? '<reason>' . $this->reason . '</reason>' : '') .
            ($this->relistItems !== null ? '<relistItems>' . $this->relistItems . '</relistItems>' : '');
    }

}