<?php

namespace Ecotone\App\Model\Order\Event;

use Ramsey\Uuid\UuidInterface;

class OrderPaymentWasSuccessful
{
    public function __construct(public readonly UuidInterface $orderId) {}
}