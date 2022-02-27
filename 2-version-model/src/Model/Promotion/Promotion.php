<?php

namespace Ecotone\App\Model\Promotion;

use Ecotone\App\Model\Order\Email;

class Promotion
{
    const MINIMUM_ORDERS_FOR_PROMOTION = 3;

    private Email $email;
    private int $amountOfOrders;

    public function __construct(Email $email)
    {
        $this->email = $email;
        $this->amountOfOrders = 0;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function increaseOrderAmount(): void
    {
        $this->amountOfOrders++;
    }

    public function isGrantedToPromotion(): bool
    {
        return $this->amountOfOrders >= self::MINIMUM_ORDERS_FOR_PROMOTION;
    }
}