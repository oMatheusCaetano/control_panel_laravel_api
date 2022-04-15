<?php

namespace App\Repositories;

use App\Models\CreditCard;
use App\Repositories\Contracts\ICreditCardRepository;

class CreditCardRepository extends Repository implements ICreditCardRepository
{
    protected $modelClass = CreditCard::class;
}
