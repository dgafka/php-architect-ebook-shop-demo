<?php

namespace Ecotone\App;

use function json_decode;
use function json_encode;

class OrderController
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function placeOrder(string $requestAsJson): void
    {
        $data = json_decode($requestAsJson, true, flags: JSON_THROW_ON_ERROR);

        $this->orderService->placeOrder($data);
    }

    public function getOrders(): string
    {
        return json_encode($this->orderService->getOrders());
    }
}