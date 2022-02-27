<?php

namespace Ecotone\App\Model;

final class Email
{
    public function __construct(public readonly string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email is incorrect: " . $address);
        }
    }
}