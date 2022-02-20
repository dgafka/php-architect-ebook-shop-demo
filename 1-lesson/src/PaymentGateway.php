<?php

namespace Ecotone\App;

class PaymentGateway
{
    public function performPayment(array $creditCard, float $amount): void
    {
        echo sprintf("Payment was performed for %d using credit card %s", $amount, $creditCard["number"]);
    }
}