<?php

namespace Ecotone\App;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Ecotone\App\Infrastructure\EbookRepository;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public function __construct(private Connection $connection, private EbookRepository $ebookRepository, private PromotionService $promotionService, private PaymentGateway $paymentGateway, private EmailService $emailService)
    {
    }

    public function placeOrder(array $data): void
    {
        $relatedEbooks = [];
        foreach ($data["ebookIds"] as $ebookId) {
            $relatedEbooks[] = $this->ebookRepository->getById($ebookId);
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
}