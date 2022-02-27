<?php

namespace Ecotone\App\Infrastructure;

use Ecotone\App\Model\Price;
use Ecotone\Messaging\Attribute\Converter;

class PriceConverter
{
    #[Converter]
    public function convertFrom(Price $price): int
    {
        return $price->amount;
    }

    #[Converter]
    public function convertTo(int $price): Price
    {
        return new Price($price);
    }
}