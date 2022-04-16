<?php

namespace App\Utils;

use Illuminate\Support\Facades\Date as FacadesDate;

class Date extends FacadesDate
{
    public const EN = 'Y-m-d';
    public const EN_WITH_TIME = 'Y-m-d H:i:s';
    public const PT = 'd/m/Y';
    public const PT_WITH_TIME = 'd/m/Y H:i:s';


    public static function enWithTime(string $date): string
    {
        $date = trim($date);
        $dateFormat = self::extractFormat($date);

        if (in_array($dateFormat, [self::PT, self::PT_WITH_TIME])) {
            $edate = explode(' ', $date);
            $day = explode('/', $edate[0])[0];
            $month = explode('/', $edate[0])[1];
            $year = explode('/', $edate[0])[2];

            $time = '00:00:00';

            if (count($edate) > 1) {
                $time = $edate[1];
            }

            return $year . '-' . $month . '-' . $day . ' ' . $time;
        }

        if (in_array($dateFormat, [self::PT, self::PT_WITH_TIME])) {
            $edate = explode(' ', $date);
            $day = explode('/', $edate[0])[0];
            $month = explode('/', $edate[0])[1];
            $year = explode('/', $edate[0])[2];

            $time = '00:00:00';

            if (count($edate) > 1) {
                $time = $edate[1];
            }

            return $year . '-' . $month . '-' . $day . ' ' . $time;
        }

        return Date::createFromFormat($dateFormat, $date)->format(Date::EN_WITH_TIME);
    }

    public static function extractFormat(string $date): ?string
    {

        $formats = [
            self::EN => '/^\d{4}-\d{2}-\d{2}$/',
            self::EN_WITH_TIME => '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            self::PT => '/^\d{2}\/\d{2}\/\d{4}$/',
            self::PT_WITH_TIME => '/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$/',
        ];

        foreach ($formats as $format => $regex) {
            if (preg_match($regex, $date)) {
                return $format;
            }
        }

        return null;
    }
}
