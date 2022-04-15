<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CreditCard>
 */
class CreditCardFactory extends Factory
{
    const BANK_NAMES = [
        ['name' => 'Nubank' ,       'bg_color' => '#820AD1', 'txt_color' => '#FFFFFF'],
        ['name' => 'Santander' ,    'bg_color' => '#EC0000', 'txt_color' => '#FFFFFF'],
        ['name' => 'Bradesco' ,     'bg_color' => '#CC092F', 'txt_color' => '#FFFFFF'],
        ['name' => 'Itaú Unibanco', 'bg_color' => '#1A5493', 'txt_color' => '#FFF212'],
        ['name' => 'Sicoob' ,       'bg_color' => '#003641', 'txt_color' => '#FFFFFF'],
    ];

    public function definition()
    {
        $fakeData = $this->faker->creditCardDetails();
        $bank = self::BANK_NAMES[random_int(0, count(self::BANK_NAMES) - 1)];

        return [
            'bank'            => $bank['name'],
            'flag'            => $fakeData['type'],
            'cardholder_name' => $fakeData['name'],
            'number'          => $fakeData['number'],
            'cvv'             => random_int(100, 999),
            'expiration'      => $fakeData['expirationDate'],
            'bg_color'        => $bank['bg_color'],
            'txt_color'       => $bank['txt_color'],
        ];
    }
}
