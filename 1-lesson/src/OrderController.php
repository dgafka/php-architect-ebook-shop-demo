<?php

namespace Ecotone\App;

use Doctrine\DBAL\Connection;

class OrderController
{
    public function __construct(private Connection $connection) {}

    public function placeOrder(string $requestAsJson): void
    {
        $data = \json_decode($requestAsJson, true, flags: JSON_THROW_ON_ERROR);


    }
}