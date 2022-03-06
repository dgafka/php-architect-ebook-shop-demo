<?php

namespace Ecotone\App;

use Ecotone\App\Infrastructure\OrderRepository;
use Ecotone\App\Model\Order\CreditCard;
use Ecotone\App\Model\Order\Email;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use Ecotone\Modelling\CommandBus;
use function json_decode;
use function json_encode;

class OrderController
{
    public function __construct(private CommandBus $commandBus, private OrderRepository $orderRepository, private Serializer $serializer)
    {
    }

    public function placeOrder(string $requestAsJson): void
    {
        $this->commandBus->sendWithRouting("placeOrder", $requestAsJson, "application/json");
    }

    public function getOrders(): string
    {
        return $this->serializer->convertFromPHP(
            $this->orderRepository->getAll(),
            "application/json"
        );
    }
}