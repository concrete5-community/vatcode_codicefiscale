<?php
namespace Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atVatcodeCodicefiscaleSettings")
 */
class VatcodeCodicefiscaleSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $akType = '';

    /**
     * @return string
     */
    public function getType()
    {
        return $this->akType;
    }

    /**
     * @param string $value
     */
    public function setType($value)
    {
        $this->akType = (string) $value;
    }
}
