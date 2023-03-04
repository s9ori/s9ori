<?php

namespace GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Models\CurrencyAmount;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CurrencyRepository;

/**
 * Currency amount adapter.
 *
 * @since 1.0.0
 */
class CurrencyAmountAdapter implements DataSourceAdapterContract
{
    /** @var float currency amount */
    private $amount;

    /** @var string currency code */
    private $currency;

    /**
     * Currency amount adapter constructor.
     *
     * @since 3.4.1
     *
     * @param float $amount
     * @param string $currency
     */
    public function __construct(float $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Converts a currency amount into a native object.
     *
     * @since 3.4.1
     *
     * @return CurrencyAmount
     */
    public function convertFromSource() : CurrencyAmount
    {
        $currencyAmount = new CurrencyAmount();

        return $currencyAmount
            ->setAmount((int) round($this->amount * $this->conversionFactor($this->currency)))
            ->setCurrencyCode($this->currency);
    }

    /**
     * Converts a currency amount to a float.
     *
     * @since 3.4.1
     *
     * @param CurrencyAmount $currencyAmount
     *
     * @return float
     */
    public function convertToSource(CurrencyAmount $currencyAmount = null) : float
    {
        if ($currencyAmount) {
            $this->amount = (float) ($currencyAmount->getAmount() / $this->conversionFactor($currencyAmount->getCurrencyCode()));
            $this->currency = $currencyAmount->getCurrencyCode();
        }

        return $this->amount;
    }

    /**
     * Get the conversion factor for a given currency.
     *
     * For decimal-based currencies converting to and from the smallest unit is accomplished by using a conversion
     * factor of 100. Some currencies do not use decimals and therefore do not need conversion.
     *
     * @param string $currencyCode
     * @return int
     */
    protected function conversionFactor(string $currencyCode) : int
    {
        if (ArrayHelper::contains(CurrencyRepository::getNoDecimalCurrencies(), strtolower($currencyCode))) {
            return 1;
        }

        return 100;
    }
}
