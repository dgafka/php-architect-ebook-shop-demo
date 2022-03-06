<?php

namespace Ecotone\App\Model\Ebook;

class RegisterEbook
{
    public readonly int $ebookId;
    public readonly string $title;
    public readonly string $content;
    public readonly Price $price;
}