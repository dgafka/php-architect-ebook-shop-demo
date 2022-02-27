<?php

namespace Ecotone\App\Model;

class Ebook
{
    private int $ebookId;
    private string $title;
    private string $content;
    private Price $price;

    public function __construct(array $data)
    {
        if (strlen($data["title"]) <= 0) {
            throw new \InvalidArgumentException("Title must contain any words");
        }
        if (strlen($data["content"]) < 10) {
            throw new \InvalidArgumentException("Content must be at least 10 characters long");
        }

        $this->ebookId = $data["ebookId"];
        $this->title = $data["title"];
        $this->content = $data["content"];
        $this->price = $data["price"];
    }
}