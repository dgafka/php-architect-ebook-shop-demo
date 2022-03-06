<?php

namespace Ecotone\App;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Ecotone\App\Infrastructure\EbookRepository;
use Ecotone\App\Infrastructure\OrderRepository;
use Ecotone\App\Infrastructure\PromotionRepository;
use Ecotone\App\Model\Ebook\Price;
use Ecotone\App\Model\Order\Event\OrderPaymentWasSuccessful;
use Ecotone\App\Model\Order\Event\OrderWasPlaced;
use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\EventBus;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public function __construct(private OrderRepository $orderRepository, private EbookRepository $ebookRepository, private PromotionRepository $promotionRepository, private EventBus $eventBus)
    {
    }

    public function placeOrder(array $data): void
    {
        $relatedEbooks = [];
        foreach ($data["ebookIds"] as $ebookId) {
            $relatedEbooks[] = $this->ebookRepository->getById($ebookId);
        }

        $price = Price::zero();
        foreach ($relatedEbooks as $ebook) {
            $price = $price->add($ebook->getPrice());
        }

        $promotion = $this->promotionRepository->getById($data['email']);
        $data["price"] = $promotion->isGrantedToPromotion() ? ($price->multiply(0.9)) : $price;

        $order = new \Ecotone\App\Model\Order\Order($data);
        $this->orderRepository->save($order);

        $this->eventBus->publish(new OrderWasPlaced($order->getOrderId()));
    }

    #[Asynchronous("order_channel")]
    #[EventHandler(endpointId: "performPayment")]
    public function performPayment(OrderWasPlaced $event, PaymentGateway $paymentGateway): void
    {
        $order = $this->orderRepository->getById($event->orderId);
        $creditCard = $order->getCreditCard();

        $paymentGateway->performPayment($creditCard, $order->getPrice());

        $this->eventBus->publish(new OrderPaymentWasSuccessful($event->orderId));
    }

    #[Asynchronous("order_channel")]
    #[EventHandler(endpointId: "sendTo")]
    public function sendTo(OrderPaymentWasSuccessful $event, EmailService $emailService): void
    {
        $order = $this->orderRepository->getById($event->orderId);
        $ebooks = array_map(fn(int $ebookId) => $this->ebookRepository->getById($ebookId), $order->getRelatedEbookIds());

        $emailService->sendTo($order->getEmail(), $ebooks);
    }

    #[Asynchronous("order_channel")]
    #[EventHandler(endpointId: "increasePromotion")]
    public function increasePromotion(OrderPaymentWasSuccessful $event): void
    {
        $order = $this->orderRepository->getById($event->orderId);

        $promotion = $this->promotionRepository->getById($order->getEmail());
        $promotion->increaseOrderAmount();
        $this->promotionRepository->save($promotion);
    }
}