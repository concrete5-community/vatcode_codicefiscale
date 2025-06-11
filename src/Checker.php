<?php

namespace VatcodeCodicefiscale;

defined('C5_EXECUTE') or die('Access Denied.');

class Checker
{
    /**
     * Value type: VAT code.
     *
     * @var string
     */
    const TYPE_VATCODE = 'vatCode';

    /**
     * Value type: codice fiscale.
     *
     * @var string
     */
    const TYPE_CODICEFISCALE = 'codiceFiscale';

    /**
     * Mapping between Codice Fiscale chars and their values to be used for checksum validation.
     *
     * @var array
     */
    private static $MAP_CODICEFISCALE = [
        '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
    ];

    /**
     * Return the normalized form of a value (strip out white spaces, make uppercase).
     *
     * @param string|mixed $value
     *
     * @return string returns an empty string if $value is not valid
     */
    public function normalize($value)
    {
        if (!is_string($value)) {
            return '';
        }
        $result = preg_replace('/\s+/', '', $value);
        if (preg_match('/^[\x20-\x7f]+$/', $result)) {
            $result = strtoupper($result);
        }

        return $result;
    }

    /**
     * Get the type of a value.
     *
     * @param string $value|mixed The value to be checked (it should be normalized with Checker::normalize)
     *
     * @return string One of the TYPE_... constants, or an empty string.
     */
    public function getType($value)
    {
        if ($this->isVatCode($value)) {
            return static::TYPE_VATCODE;
        }
        if ($this->isCodiceFiscale($value)) {
            return static::TYPE_CODICEFISCALE;
        }

        return '';
    }

    /**
     * Check if a value contains a valid VAT code string.
     *
     * @param string|mixed $value The value to be checked (it should be normalized with Checker::normalize)
     *
     * @return bool
     */
    public function isVatCode($value)
    {
        if (!is_string($value) || !preg_match('/^(IT)?[0-9]{11}$/', $value)) {
            return false;
        }
        if (strpos($value, 'IT') === 0) {
            $value = substr($value, 2);
        }
        $sum = 0;
        for ($i = 0; $i <= 9; $i += 2) {
            $sum += (int) $value[$i];
        }
        for ($i = 1; $i <= 9; $i += 2) {
            $c = 2 * (int) $value[$i];
            if ($c > 9) {
                $c -= 9;
            }
            $sum += $c;
        }
        $checkCode = (10 - ($sum % 10)) % 10;

        return $checkCode === (int) $value[10];
    }

    /**
     * Check if a value contains a valid codice fiscale string.
     *
     * @param string|mixed $value The value to be checked (it should be normalized with Checker::normalize)
     *
     * @return bool
     */
    public function isCodiceFiscale($value)
    {
        if (!is_string($value) && !preg_match('/^[A-Z]{6}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{3}[A-Z]{1}$/', $value)) {
            return false;
        }
        $sum = 0;
        for ($i = 1; $i <= 13; $i += 2) {
            $char = $value[$i];
            if ($char >= '0' && $char <= '9') {
                $sum += (int) $char;
            } else {
                $sum += ord($char) - ord('A');
            }
        }
        for ($i = 0; $i <= 14; $i += 2) {
            $char = $value[$i];
            $sum += static::$MAP_CODICEFISCALE[$char];
        }
        $checkChar = chr($sum % 26 + ord('A'));

        return $value[15] === $checkChar;
    }
}
