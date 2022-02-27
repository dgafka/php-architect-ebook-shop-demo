<?php

namespace Ecotone\App\Model\Ebook;

final class Price
{
    public function __construct(public readonly float $amount)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Price must be higher than 0");
        }
    }

    public function add(self $price): self
    {
        return new self($this->amount + $price->amount);
    }

    public function multiply(float $multiplyBy): self
    {
        return new self($this->amount * $multiplyBy);
    }
}