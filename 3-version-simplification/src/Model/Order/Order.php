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

    public function __construct(array $data)
    {
        $this->orderId = Uuid::uuid4();
        $this->email = $data['email'];
        $this->creditCard = $data['creditCard'];
        $this->relatedEbookIds = $data['ebookIds'];
        $this->price = $data['price'];
        $this->occurredAt = new \DateTimeImmutable('now');
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
}