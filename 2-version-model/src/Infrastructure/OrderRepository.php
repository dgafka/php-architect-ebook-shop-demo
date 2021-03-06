<?php

namespace Ecotone\App\Infrastructure;

use Doctrine\DBAL\Connection;
use Ecotone\App\Model\Order\Order;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Gateway\Converter\Serializer;

class OrderRepository
{
    public function __construct(private Connection $connection, private Serializer $serializer)
    {
    }

    public function save(Order $order): void
    {
        $data = $this->serializer->convertFromPHP($order, MediaType::APPLICATION_X_PHP_ARRAY);
        $data["relatedEbookIds"] = \json_encode($data["relatedEbookIds"]);
        $data["creditCard"] = \json_encode($data['creditCard']);

        $this->connection->insert("orders", $this->convertCamelCaseToUnderscores($data));
    }

    /**
     * @return Order[]
     */
    public function getAll(): array
    {
        $data = $this->connection->executeQuery(sprintf(<<<SQL
    SELECT * FROM %s
SQL, "orders")
        )->fetchAllAssociative();

        return array_map(function(array $order) {
            $order = $this->underscoresToCamelCase($order);
            $order["relatedEbookIds"] = \json_decode($order["relatedEbookIds"], true);
            $order["creditCard"] = \json_decode($order["creditCard"], true);

            return $this->serializer->convertToPHP(
                    $order,
                MediaType::APPLICATION_X_PHP_ARRAY,
                    Order::class
                );
            },
            $data
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
}