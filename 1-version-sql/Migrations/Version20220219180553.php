<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220219180553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
    CREATE TABLE orders (
        order_id VARCHAR(36) PRIMARY KEY, 
        email VARCHAR(255), 
        credit_card_number VARCHAR(19), 
        related_ebook_ids JSON, 
        price FLOAT, 
        occurred_at TIMESTAMP
    )
SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
