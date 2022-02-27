<?php

namespace Ecotone\App;

use Ecotone\App\Model\Ebook\Ebook;
use Ecotone\App\Model\Order\Email;

class EmailService
{
    /**
     * @param Ebook[] $ebooks
     */
    public function sendTo(Email $email, array $ebooks): void
    {
        $titles = [];
        foreach ($ebooks as $ebook) {
            $titles[] = $ebook->getTitle();
        }

        echo sprintf("Email sent to %s with given titles: %s\n", $email->address, implode(",", $titles));
    }
}