<?php

namespace Ecotone\App;

use Doctrine\DBAL\Connection;
use Ecotone\App\Model\Order\Email;

class PromotionService
{
    public function __construct(private Connection $connection)
    {
    }

    public function isGrantedToPromotion(Email $emailAddress): bool
    {
        if ($promotion = $this->getCurrentPromotion($emailAddress)) {
            return $promotion["amount_of_orders"] >= 3;
        }

        return false;
    }

    public function increaseOrderAmount(Email $email): void
    {
        if ($promotion = $this->getCurrentPromotion($email)) {
            $this->connection->update("promotions", ["amount_of_orders" => $promotion["amount_of_orders"] + 1], ["email" => $email->address]);

            return;
        }

        $this->connection->insert("promotions", ["email" => $email->address, "amount_of_orders" => 1]);
    }

    private function getCurrentPromotion(Email $email): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
    SELECT * FROM promotions WHERE email = :emailAddress
SQL, ["emailAddress" => $email->address])->fetchAssociative();

        return $result ?: null;
    }
}