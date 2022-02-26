<?php

namespace Ecotone\App;

class EmailService
{
    public function sendTo(string $email, array $ebooks): void
    {
        $titles = [];
        foreach ($ebooks as $ebook) {
            $titles[] = $ebook["title"];
        }

        echo sprintf("Email sent to %s with given titles: %s\n", $email, implode(",", $titles));
    }
}