<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditCard extends BaseModel
{
    use HasFactory;

    protected $table = 'credit_cards';
    protected $fillable = [
        'bank',
        'flag',
        'cardholder_name',
        'number',
        'cvv',
        'expiration',
        'bg_color',
        'txt_color',
    ];
}
