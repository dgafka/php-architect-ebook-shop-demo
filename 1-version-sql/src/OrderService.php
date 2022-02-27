<?php

namespace Ecotone\App;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public function __construct(private Connection $connection, private EbookService $ebookService, private PromotionService $promotionService, private PaymentGateway $paymentGateway, private EmailService $emailService)
    {
    }

    public function placeOrder(array $data): void
    {
        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email is incorrect: " . $data["email"]);
        }
        if (!($data["creditCard"]["validTillMonth"] >= 1 && $data["creditCard"]["validTillMonth"] <= 12)) {
            throw new \InvalidArgumentException("Month validity must between 1-12, got: " . $data["creditCard"]["validTillMonth"]);
        }
        if (strlen($data["creditCard"]["validTillYear"]) !== 4) {
            throw new \InvalidArgumentException("Year must have 4 characters");
        }
        if (strlen($data["creditCard"]["cvc"]) !== 3) {
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
        $price = $this->promotionService->isGrantedToPromotion($data["email"]) ? ($price * 0.9) : $price;

        $this->connection->beginTransaction();
        try {
            $this->saveOrder($data, $price);
            $this->promotionService->increaseOrderAmount($data["email"]);
            $this->paymentGateway->performPayment($data["creditCard"], $price);
            $this->emailService->sendTo($data["email"], $relatedEbooks);

            $this->connection->commit();
        }catch (\Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    public function getOrders(): array
    {
        return $this->connection->executeQuery(<<<SQL
    SELECT * FROM orders;
SQL)->fetchAllAssociative();
    }

    private function saveOrder(array $data, float $price): void
    {
        $this->connection->insert(
            "orders",
            [
                "order_id" => Uuid::uuid4()->toString(),
                "email" => $data["email"],
                "credit_card_number" => $data["creditCard"]["number"],
                "related_ebook_ids" => json_encode($data["ebookIds"]),
                "price" => $price,
                "occurred_at" => (new DateTimeImmutable())->format('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * This validates credit card number using Luhn algorithm
     * @link https://en.wikipedia.org/wiki/Luhn_algorithm
     */
    private function validateLuhn(string $number): bool
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