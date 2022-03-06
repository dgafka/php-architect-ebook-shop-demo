<?php

namespace Ecotone\App\Infrastructure\Converter;

use Ecotone\App\Model\Order\CreditCard;
use Ecotone\Messaging\Attribute\Converter;

class CreditCardConverter
{
    #[Converter]
    public function to(array $data): CreditCard
    {
        return new CreditCard(
            $data['number'],
            $data['cvc'],
            $data['validTillYear'],
            $data['validTillMonth']
        );
    }
}