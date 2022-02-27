<?php

namespace Ecotone\App\Model\Order;

final class CreditCard
{
    public function __construct(
        public readonly string $number,
        public readonly int $cvc,
        public readonly int $validTillYear,
        public readonly int $validTillMonth
    ) {
        if (!($validTillMonth >= 1 && $validTillMonth <= 12)) {
            throw new \InvalidArgumentException("Month validity must between 1-12, got: " . $data["creditCard"]["validTillMonth"]);
        }
        if (strlen($validTillYear) !== 4) {
            throw new \InvalidArgumentException("Year must have 4 characters");
        }
        if (strlen($cvc) !== 3) {
            throw new \InvalidArgumentException("Cvc code must be contain 3 characters");
        }
        if (!$this->validateLuhn($number)) {
            throw new \InvalidArgumentException("Credit card number must be valid");
        }
    }

    /**
     * This validates credit card number using Luhn algorithm
     * @link https://en.wikipedia.org/wiki/Luhn_algorithm
     */
    private function validateLuhn(string $number): bool
    {
        $sum = 0;
        $flag = 0;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $add = $flag++ & 1 ? $number[$i] * 2 : $number[$i];
            $sum += $add > 9 ? $add - 9 : $add;
        }

        return $sum % 10 === 0;
    }
}