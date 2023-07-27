<?php
namespace App\Helpers\API;


use App\Models\Currency;

class Context
{
    private static $currency;

    /**
     * @return mixed
     */
    public static function getCurrency()
    {
        if (!self::$currency) {
            $defaultCurrency = Currency::getDefaultCurrency();
            self::$currency = $defaultCurrency->code;
        }
        return self::$currency;
    }

    /**
     * @param mixed $currency
     */
    public static function setCurrency($currency): void
    {
        self::$currency = $currency;
    }
}
