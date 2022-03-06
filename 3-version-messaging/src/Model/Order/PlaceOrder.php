<?php

namespace Ecotone\App\Model\Order;

class PlaceOrder
{
    public readonly Email $email;
    public readonly CreditCard $creditCard;
    /**
     * @var int[]
     */
    public readonly array $ebookIds;
}