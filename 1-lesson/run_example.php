<?php

use Doctrine\DBAL\Connection;
use Ecotone\App\BookController;

require __DIR__ . '/vendor/autoload.php';

$container = new DI\Container();
/** @var Connection $connection */
$connection = include_once __DIR__ . '/migrations-db.php';
$container->set(Connection::class, $connection);

// prepare for rerunning example
$connection->executeStatement(<<<SQL
    DELETE FROM ebook;
SQL);

/** @var BookController $ebookController */
$ebookController = $container->get(BookController::class);

$ebookId = 1;
$ebookController->registerEbook(\json_encode([
    "ebookId" => $ebookId,
    "title" => "Happy Dog Story",
    "content" => "Dog went for work a walk and is happy because of that!",
    "price" => 10
]));

echo "Ebook {$ebookId} was registered\n";

$ebookController->updateEbook(\json_encode([
    "ebookId" => $ebookId,
    "price" => 15
]));

echo "Ebook {$ebookId} was updated\n";

echo sprintf("Current ebook: %s\n", $ebookController->getEbook($ebookId));