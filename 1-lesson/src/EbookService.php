<?php

namespace Ecotone\App;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;

class EbookService
{
    public function __construct(private Connection $connection)
    {
    }

    public function registerEbook(array $data): void
    {
        $this->validateData($data);

        $this->connection->executeStatement(<<<SQL
    INSERT INTO ebooks (ebook_id, title, content, price) VALUES (:ebookId, :title, :content, :price) 
SQL, ["ebookId" => $data["ebookId"], "title" => $data["title"], "content" => $data["content"], "price" => $data["price"]]);
    }

    public function updateEbook(array $data): void
    {
        $ebook = $this->getEbookById($data["ebookId"]);

        $data = array_merge($ebook, $data);
        $this->validateData($data);

        $this->connection->executeStatement(<<<SQL
    UPDATE ebooks SET title = :title, content = :content, price = :price WHERE ebook_id = :ebookId 
SQL, ["ebookId" => $data["ebookId"], "title" => $data["title"], "content" => $data["content"], "price" => $data["price"]]);
    }

    public function getEbookById(mixed $ebookId): array
    {
        $ebook = $this->connection->executeQuery(<<<SQL
    SELECT * FROM ebooks WHERE ebook_id = :ebookId
SQL, ["ebookId" => $ebookId])->fetchAssociative();

        if (!$ebook) {
            throw new InvalidArgumentException("Ebook to update not found");
        }
        return $ebook;
    }

    private function validateData(array $data): void
    {
        if ($data["price"] <= 0) {
            throw new InvalidArgumentException("Ebook price must be higher than 0");
        }
        if (strlen($data["title"]) <= 0) {
            throw new InvalidArgumentException("Title must contain any words");
        }
        if (strlen($data["content"]) < 10) {
            throw new InvalidArgumentException("Content must be at least 10 characters long");
        }
    }
}