<?php

namespace Ecotone\App\Model\Order\Event;

use Ramsey\Uuid\UuidInterface;

class OrderWasPlaced
{
    public function __construct(public readonly UuidInterface $orderId) {}
}