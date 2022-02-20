<?php

namespace Ecotone\App;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public function __construct(private Connection $connection, private EbookService $ebookService, private PaymentGateway $paymentGateway, private EmailService $emailService)
    {
    }

    public function placeOrder(array $data): void
    {
        $relatedEbooks = [];
        foreach ($data["ebookIds"] as $ebookId) {
            $relatedEbooks[] = $this->ebookService->getEbookById($ebookId);
        }

        $price = 0;
        foreach ($relatedEbooks as $ebook) {
            $price += $ebook["price"];
        }

        $this->paymentGateway->performPayment($data["creditCard"], $price);
        $this->emailService->sendTo($data["email"], $relatedEbooks);
        $this->saveOrder($data, $price);
    }

    public function getOrders(): array
    {
        return $this->connection->executeQuery(<<<SQL
    SELECT * FROM orders;
SQL)->fetchAllAssociative();
    }

    private function saveOrder(array $data, float $price): void
    {
        $this->connection->executeStatement(<<<SQL
    INSERT INTO orders (order_id, email, credit_card_number, related_ebook_ids, price, occurred_at)
    VALUES (:orderId, :email, :creditCardNumber, :relatedEbookIds, :price, :occurredAt)
SQL, [
            "orderId" => Uuid::uuid4()->toString(),
            "email" => $data["email"],
            "creditCardNumber" => $data["creditCard"]["number"],
            "relatedEbookIds" => json_encode($data["ebookIds"]),
            "price" => $price,
            "occurredAt" => (new DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }
}