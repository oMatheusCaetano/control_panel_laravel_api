<?php

namespace Tests\Feature\CreditCard;

use App\Models\CreditCard;
use Tests\Feature\ListTestClass;

class ListCreditCardsTest extends ListTestClass
{
    protected string $modelClass = CreditCard::class;
    protected string $endpoint = 'api/credit-cards';
    protected array $stringFields = [
        'bank',
        'flag',
        // 'cardholder_name',
        // 'cvv',
        // 'expiration',
        // 'bg_color',
        // 'txt_color',
    ];
}
