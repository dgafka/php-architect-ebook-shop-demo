<?php

namespace Ecotone\App\Model\Ebook;

use Ecotone\Modelling\Attribute\Aggregate;
use Ecotone\Modelling\Attribute\AggregateIdentifier;
use Ecotone\Modelling\Attribute\CommandHandler;

#[Aggregate]
class Ebook
{
    #[AggregateIdentifier]
    private int $ebookId;
    private string $title;
    private string $content;
    private Price $price;

    public function __construct(RegisterEbook $command)
    {
        if (strlen($command->title) <= 0) {
            throw new \InvalidArgumentException("Title must contain any words");
        }
        if (strlen($command->content) < 10) {
            throw new \InvalidArgumentException("Content must be at least 10 characters long");
        }

        $this->ebookId = $command->ebookId;
        $this->title = $command->title;
        $this->content = $command->content;
        $this->price = $command->price;
    }

    #[CommandHandler("registerEbook")]
    public static function register(RegisterEbook $command): self
    {
        return new self($command);
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}