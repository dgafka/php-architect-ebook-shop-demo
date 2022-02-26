<?php

namespace Ecotone\App;

use Doctrine\DBAL\Connection;

class PromotionService
{
    public function __construct(private Connection $connection)
    {
    }

    public function isGrantedToPromotion(string $emailAddress): bool
    {
        if ($promotion = $this->getCurrentPromotion($emailAddress)) {
            return $promotion["amount_of_orders"] >= 3;
        }

        return false;
    }

    public function increaseOrderAmount(string $emailAddress): void
    {
        if ($promotion = $this->getCurrentPromotion($emailAddress)) {
            $this->connection->update("promotions", ["amount_of_orders" => $promotion["amount_of_orders"] + 1], ["email" => $emailAddress]);

            return;
        }

        $this->connection->insert("promotions", ["email" => $emailAddress, "amount_of_orders" => 1]);
    }

    private function getCurrentPromotion(string $emailAddress): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
    SELECT * FROM promotions WHERE email = :emailAddress
SQL, ["emailAddress" => $emailAddress])->fetchAssociative();

        return $result ?: null;
    }
}