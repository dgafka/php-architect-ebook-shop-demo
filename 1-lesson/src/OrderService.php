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
        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email is incorrect: " . $data["email"]);
        }
        if (!is_int($data["creditCard"]["validTillMonth"]) || !($data["creditCard"]["validTillMonth"] >= 1 && $data["creditCard"]["validTillMonth"] <= 12)) {
            throw new \InvalidArgumentException("Month validity must between 1-12, got: " . $data["creditCard"]["validTillMonth"]);
        }
        if (!is_int($data["creditCard"]["validTillYear"])) {
            throw new \InvalidArgumentException("Year validity must be integer");
        }
        if (!is_int($data["creditCard"]["cvc"]) && strlen($data["creditCard"]["cvc"]) === 3) {
            throw new \InvalidArgumentException("Cvc code must be contain 3 characters");
        }
        if (!$this->validateLuhn($data["creditCard"]["number"])) {
            throw new \InvalidArgumentException("Credit card number must be valid");
        }

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

    /**
     * This validates credit card number using Luhn algorithm
     * @link https://en.wikipedia.org/wiki/Luhn_algorithm
     */
    function validateLuhn(string $number): bool
    {
        $sum = 0;
        $flag = 0;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $add = $flag++ & 1 ? $number[$i] * 2 : $number[$i];
            $sum += $add > 9 ? $add - 9 : $add;
        }

        return $sum % 10 === 0;
    }
}