<?php

namespace Ecotone\App\Infrastructure;

use Doctrine\DBAL\Connection;
use Ecotone\App\Model\Order\Order;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ramsey\Uuid\UuidInterface;

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

    public function getById(UuidInterface $orderId): Order
    {
        $data = $this->connection->executeQuery(sprintf(<<<SQL
    SELECT * FROM %s WHERE %s = :id
SQL, "orders", "order_id"),
            ["id" => $orderId->toString()]
        )->fetchAssociative();

        if (!$data) {
            throw new \InvalidArgumentException("Order not found");
        }

        $data = $this->underscoresToCamelCase($data);
        $data["relatedEbookIds"] = \json_decode($data["relatedEbookIds"], true);
        $data["creditCard"] = \json_decode($data["creditCard"], true);

        return $this->serializer->convertToPHP(
            $data,
            MediaType::APPLICATION_X_PHP_ARRAY,
            Order::class
        );
    }

    /**
     * @return Order[]
     */
    #[QueryHandler("getAllOrders")]
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