<?php

namespace Ecotone\App;

use Ecotone\App\Infrastructure\OrderRepository;
use Ecotone\App\Model\Order\CreditCard;
use Ecotone\App\Model\Order\Email;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use function json_decode;
use function json_encode;

class OrderController
{
    public function __construct(private OrderService $orderService, private OrderRepository $orderRepository, private Serializer $serializer)
    {
    }

    public function placeOrder(string $requestAsJson): void
    {
        $data = json_decode($requestAsJson, true, flags: JSON_THROW_ON_ERROR);
        $data['email'] = new Email($data['email']);
        $data['creditCard'] = new CreditCard(
            $data['creditCard']['number'],
            $data['creditCard']['cvc'],
            $data['creditCard']['validTillYear'],
            $data['creditCard']['validTillMonth']
        );

        $this->orderService->placeOrder($data);
    }

    public function getOrders(): string
    {
        return $this->serializer->convertFromPHP(
            $this->orderRepository->getAll(),
            "application/json"
        );
    }
}