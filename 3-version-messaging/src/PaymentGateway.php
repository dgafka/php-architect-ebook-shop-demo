<?php

namespace Ecotone\App;

use Ecotone\App\Model\Ebook\Price;
use Ecotone\App\Model\Order\CreditCard;

class PaymentGateway
{
    public function performPayment(CreditCard $creditCard, Price $price): void
    {
        echo sprintf("Payment was performed for %d using credit card number: %s, valid till: %s/%s and cvc: %s\n", $price->amount, $creditCard->number, $creditCard->validTillMonth, $creditCard->validTillYear, $creditCard->cvc);
    }
}