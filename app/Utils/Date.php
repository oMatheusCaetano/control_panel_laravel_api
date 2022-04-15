<?php

namespace App\Utils;

use Illuminate\Support\Facades\Date as FacadesDate;

class Date extends FacadesDate
{
    public const EN = 'Y-m-d';
    public const EN_WITH_TIME = 'Y-m-d H:i:s';

    public static function enWithTime(string $date): string
    {
        return Date::createFromFormat(Date::EN_WITH_TIME, $date)->format(Date::EN_WITH_TIME);
    }
}
