<?php

namespace Ecotone\App;

use Ecotone\App\Model\Email;

class EmailService
{
    public function sendTo(Email $email, array $ebooks): void
    {
        $titles = [];
        foreach ($ebooks as $ebook) {
            $titles[] = $ebook["title"];
        }

        echo sprintf("Email sent to %s with given titles: %s\n", $email->address, implode(",", $titles));
    }
}