<?php

namespace Ecotone\App\Infrastructure\Converter;

use Ecotone\App\Model\Order\Email;
use Ecotone\Messaging\Attribute\Converter;

class EmailConverter
{
    #[Converter]
    public function from(Email $email): string
    {
        return $email->address;
    }

    #[Converter]
    public function to(string $emailAddress): Email
    {
        return new Email($emailAddress);
    }
}