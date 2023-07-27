<?php

use App\Models\Currency;
use App\Helpers\API\Context;
use App\Models\Option;

if (!function_exists('get_option')) {
    function get_option($name, $default = null)
    {
        return Option::getOption($name, $default);
    }
}

if (!function_exists('convert_currency')) {
    function convert_currency($amount, $to, $from = null)
    {
        if (!$from) {
            $from = Currency::getDefaultCurrency();
        } elseif (is_string($from)) {
            $from = Currency::query()->where('code', $from)->first();
        }
        $to = Currency::query()->where('code', $to)->first();
        if (!($from instanceof Currency) || !($to instanceof Currency) || !is_numeric($amount)) throw new Exception('Unable to convert currency');
        return $amount * ($to->rate / $from->rate);
    }
}

if (!function_exists('as_currency')) {
    function as_currency($amount)
    {
        $defaultCurrency = Currency::getDefaultCurrency();
        $requestCurrencyCode = Context::getCurrency();
        if (!$requestCurrencyCode) return round($amount, config('proxime.decimals'));
        if (strtolower($defaultCurrency->code) != strtolower($requestCurrencyCode)) {
            return round(convert_currency($amount, $requestCurrencyCode, $defaultCurrency->code), config('proxime.decimals'));
        }
        return round($amount, config('proxime.decimals'));
    }
}

if (!function_exists('array_cartesian')) {
    /**
     * Find all possible combinations of values from the input array and return in a logical order.
     *
     * @param array $input Input.
     * @return array
     * @since 2.5.0
     */
    function array_cartesian($input)
    {
        $input = array_filter($input);
        $results = array();
        $indexes = array();
        $index = 0;

        // Generate indexes from keys and values so we have a logical sort order.
        foreach ($input as $key => $values) {
            foreach ($values as $value) {
                $indexes[$key][$value] = $index++;
            }
        }

        // Loop over the 2D array of indexes and generate all combinations.
        foreach ($indexes as $key => $values) {
            // When result is empty, fill with the values of the first looped array.
            if (empty($results)) {
                foreach ($values as $value) {
                    $results[] = array($key => $value);
                }
            } else {
                // Second and subsequent input sub-array merging.
                foreach ($results as $result_key => $result) {
                    foreach ($values as $value) {
                        // If the key is not set, we can set it.
                        if (!isset($results[$result_key][$key])) {
                            $results[$result_key][$key] = $value;
                        } else {
                            // If the key is set, we can add a new combination to the results array.
                            $new_combination = $results[$result_key];
                            $new_combination[$key] = $value;
                            $results[] = $new_combination;
                        }
                    }
                }
            }
        }

        // Sort the indexes.
        arsort($results);

        // Convert indexes back to values.
        foreach ($results as $result_key => $result) {
            $converted_values = array();

            // Sort the values.
            arsort($results[$result_key]);

            // Convert the values.
            foreach ($results[$result_key] as $key => $value) {
                $converted_values[$key] = array_search($value, $indexes[$key], true);
            }

            $results[$result_key] = $converted_values;
        }

        return $results;
    }
}
