<?php

namespace GoDaddy\WordPress\MWC\Common\Models;

/**
 * An object representation of a currency amount.
 */
class CurrencyAmount extends AbstractModel
{
    /** @var int in cents */
    protected $amount;

    /** @var string 2-letter Unicode CLDR currency code */
    protected $currencyCode;

    /**
     * Gets the amount.
     *
     * @return int cents
     */
    public function getAmount() : int
    {
        return is_int($this->amount) ? $this->amount : 0;
    }

    /**
     * Gets the currency code.
     *
     * @return string 3-letter Unicode CLDR currency code
     */
    public function getCurrencyCode() : string
    {
        return is_string($this->currencyCode) ? $this->currencyCode : '';
    }

    /**
     * Sets the amount.
     *
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount) : CurrencyAmount
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Sets the currency code.
     *
     * @param string $code 3-letter Unicode CLDR currency code
     * @return $this
     */
    public function setCurrencyCode(string $code) : CurrencyAmount
    {
        $this->currencyCode = $code;

        return $this;
    }

    /**
     * Returns a formatted string with the currency symbol and amount, in either HTML or plain text format.
     *
     * @param bool $preserveHtmlTags whether to preserve HTML tags
     * @return string
     */
    public function toFormattedString(bool $preserveHtmlTags = false) : string
    {
        $formattedString = wc_price((float) wc_remove_number_precision($this->getAmount()), ['currency' => $this->getCurrencyCode()]);

        return $preserveHtmlTags ? $formattedString : strip_tags($formattedString);
    }
}
