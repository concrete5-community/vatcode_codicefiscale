<?php

namespace Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Doctrine\ORM\Mapping as ORM;
use VatcodeCodicefiscale\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @ORM\Entity
 * @ORM\Table(name="atVatcodeCodicefiscaleSettings")
 */
class VatcodeCodicefiscaleSettings extends Settings
{
    /**
     * The type of the field.
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    protected $akType;

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
    }
}
