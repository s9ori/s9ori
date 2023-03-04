<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * Repository for handling WooCommerce amounts.
 */
class CurrencyRepository
{
    /**
     * Converts the string amount from WooCommerce to a integer in cents.
     *
     * @param string $amount
     * @return int
     */
    public static function getStripeAmount(string $amount) : int
    {
        if (ArrayHelper::contains(self::getNoDecimalCurrencies(), strtolower(get_woocommerce_currency()))) {
            return absint($amount);
        } else {
            return absint(wc_format_decimal(wc_add_number_precision((float) $amount), wc_get_price_decimals()));
        }
    }

    /**
     * Currencies that are zero decimals.
     *
     * @return string[]
     */
    public static function getNoDecimalCurrencies() : array
    {
        return [
            'bif', // Burundian Franc
            'clp', // Chilean Peso
            'djf', // Djiboutian Franc
            'gnf', // Guinean Franc
            'jpy', // Japanese Yen
            'kmf', // Comorian Franc
            'krw', // South Korean Won
            'mga', // Malagasy Ariary
            'pyg', // Paraguayan Guaraní
            'rwf', // Rwandan Franc
            'ugx', // Ugandan Shilling
            'vnd', // Vietnamese Đồng
            'vuv', // Vanuatu Vatu
            'xaf', // Central African Cfa Franc
            'xof', // West African Cfa Franc
            'xpf', // Cfp Franc
        ];
    }
}
