<?php

namespace Ecotone\App\Infrastructure\Converter;

use Ecotone\Messaging\Attribute\Converter;

class DateTimeConverter
{
    #[Converter]
    public function from(\DateTimeImmutable $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    #[Converter]
    public function to(string $dateTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($dateTime);
    }
}