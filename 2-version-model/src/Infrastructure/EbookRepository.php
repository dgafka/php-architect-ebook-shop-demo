<?php

namespace Ecotone\App\Infrastructure;

use Doctrine\DBAL\Connection;
use Ecotone\App\Model\Ebook;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Gateway\Converter\Serializer;

class EbookRepository
{
    public function __construct(private Connection $connection, private Serializer $serializer)
    {
    }

    public function save(Ebook $ebook): void
    {
        $data = $this->serializer->convertFromPHP($ebook, MediaType::APPLICATION_X_PHP_ARRAY);

        $this->connection->insert("ebooks", $this->convertCamelCaseToUnderscores($data));
    }

    public function getById(int $ebookId): Ebook
    {
        $data = $this->connection->executeQuery(sprintf(<<<SQL
    SELECT * FROM %s WHERE %s = :id
SQL, "ebooks", "ebook_id"),
            ["id" => $ebookId]
        )->fetchAssociative();

        if (!$data) {
            throw new \InvalidArgumentException("Ebook not found");
        }

        return $this->serializer->convertToPHP(
            $this->underscoresToCamelCase($data),
            MediaType::APPLICATION_X_PHP_ARRAY,
            Ebook::class
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