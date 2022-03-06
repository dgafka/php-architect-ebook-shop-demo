<?php

namespace Ecotone\App\Infrastructure;

use Doctrine\DBAL\Connection;
use Ecotone\App\Model\Order\Email;
use Ecotone\App\Model\Promotion\Promotion;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Gateway\Converter\Serializer;

class PromotionRepository
{
    public function __construct(private Connection $connection, private Serializer $serializer)
    {
    }

    public function save(Promotion $promotion): void
    {
        $data = $this->convertCamelCaseToUnderscores($this->serializer->convertFromPHP($promotion, MediaType::APPLICATION_X_PHP_ARRAY));
        if ($this->getPromotionData($promotion->getEmail())) {
            $this->connection->update("promotions", $data, ["email" => $promotion->getEmail()->address]);

            return;
        }

        $this->connection->insert("promotions", $data);
    }

    public function getById(Email $email): Promotion
    {
        $data = $this->getPromotionData($email);

        if (!$data) {
            return new Promotion($email);
        }

        return $this->serializer->convertToPHP(
            $this->underscoresToCamelCase($data),
            MediaType::APPLICATION_X_PHP_ARRAY,
            Promotion::class
        );
    }

    private function convertCamelCaseToUnderscores(array $data): array
    {
        $underscoreData = [];
        foreach ($data as $name => $value) {
            $underscoreName = strtolower(preg_replace('/[A-Z]+/', "_" . '\\0', $name));
            $underscoreData[$underscoreName] = $value;
        }

        return $underscoreData;
    }

    private function underscoresToCamelCase(array $data): array
    {
        $camelCaseData = [];
        foreach ($data as $name => $value) {
            $camelCaseName = str_replace("_", '', ucwords($name, "_"));
            $camelCaseData[lcfirst($camelCaseName)] = $value;
        }

        return $camelCaseData;
    }

    private function getPromotionData(Email $email): ?array
    {
        $data = $this->connection->executeQuery(sprintf(<<<SQL
    SELECT * FROM %s WHERE %s = :email
SQL, "promotions", "email"),
            ["email" => $email->address]
        )->fetchAssociative();

        return $data ?: null;
    }
}