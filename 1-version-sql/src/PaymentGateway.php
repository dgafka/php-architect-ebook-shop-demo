<?php

namespace Ecotone\App;

class PaymentGateway
{
    public function performPayment(array $creditCard, float $amount): void
    {
        echo sprintf("Payment was performed for %d using credit card number: %s, valid till: %s/%s and cvc: %s\n", $amount, $creditCard["number"], $creditCard["validTillMonth"], $creditCard["validTillYear"], $creditCard["cvc"]);
    }
}