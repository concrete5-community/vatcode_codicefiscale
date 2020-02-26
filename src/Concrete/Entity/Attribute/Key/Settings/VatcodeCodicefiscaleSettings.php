<?php

namespace Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use VatcodeCodicefiscale\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @\Doctrine\ORM\Mapping\Entity
 * @\Doctrine\ORM\Mapping\Table(name="atVatcodeCodicefiscaleSettings")
 */
class VatcodeCodicefiscaleSettings extends Settings
{
    /**
     * The type of the field.
     *
     * @\Doctrine\ORM\Mapping\Column(type="string", length=20, nullable=false)
     */
    protected $akType = '';

    /**
     * Allow invalid values?
     *
     * @\Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $allowInvalidValues = false;

    /**
     * Get the type of the attribute (one of the Checked::TYPE_... constants, or an empty string for any).
     *
     * @return string
     */
    public function getType()
    {
        return (string) $this->akType;
    }

    /**
     * Set the type of the attribute (one of the Checked::TYPE_... constants) or NULL for any.
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setType($value)
    {
        $akType = '';
        if ($value) {
            $value = (string) $value;
            switch ($value) {
                case Checker::TYPE_CODICEFISCALE:
                case Checker::TYPE_VATCODE:
                    $akType = $value;
                    break;
            }
        }
        $this->akType = $akType;

        return $this;
    }

    /**
     * Allow invalid values?
     *
     * @return bool
     */
    public function isAllowInvalidValues()
    {
        return (bool) $this->allowInvalidValues;
    }

    /**
     * Allow invalid values?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setAllowInvalidValues($value)
    {
        $this->allowInvalidValues = (bool) $value;

        return $this;
    }
}
