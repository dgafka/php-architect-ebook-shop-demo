<?php

namespace Ecotone\App\Model\Order;

use Ecotone\App\Model\Ebook\Price;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Order
{
    private UuidInterface $orderId;
    private Email $email;
    private CreditCard $creditCard;
    /** @var int[] */
    private array $relatedEbookIds;
    private Price $price;
    private \DateTimeImmutable $occurredAt;

    public function __construct(PlaceOrder $command, Price $price)
    {
        $this->orderId = Uuid::uuid4();
        $this->email = $command->email;
        $this->creditCard = $command->creditCard;
        $this->relatedEbookIds = $command->ebookIds;
        $this->price = $price;
        $this->occurredAt = new \DateTimeImmutable('now');
    }

    public function getOrderId(): UuidInterface
    {
        return $this->orderId;
    }

    public function getCreditCard(): CreditCard
    {
        return $this->creditCard;
    }

    /**
     * @return int[]
     */
    public function getRelatedEbookIds(): array
    {
        return $this->relatedEbookIds;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}