<?php

namespace Concrete\Package\VatcodeCodicefiscale\Attribute\VatcodeCodicefiscale;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Concrete\Core\Error\UserMessageException;
use Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings\VatcodeCodicefiscaleSettings;
use SimpleXMLElement;
use VatcodeCodicefiscale\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @method \Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings\VatcodeCodicefiscaleSettings getAttributeKeySettings()
 */
class Controller extends DefaultController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::$helpers
     */
    protected $helpers = ['form'];

    /**
     * {@inheritdoc}
     */
    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::getAttributeKeySettingsClass()
     */
    public function getAttributeKeySettingsClass()
    {
        return VatcodeCodicefiscaleSettings::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::getIconFormatter()
     */
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('id-card-o');
    }

    /**
     * Initialize the form to be used to configure an attribute key.
     */
    public function type_form()
    {
        $settings = $this->getAttributeKeySettings();
        $this->set('type', $settings->getType());
        $this->set('allowInvalidValues', $settings->isAllowInvalidValues());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::saveKey()
     */
    public function saveKey($data)
    {
        $data = (is_array($data) ? $data : []) + [
            'type' => '',
            'allowInvalidValues' => false,
        ];

        return $this->getAttributeKeySettings()
            ->setType($data['type'])
            ->setAllowInvalidValues($data['allowInvalidValues'])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::form()
     */
    public function form()
    {
        $this->requireAsset('vatcode_codicefiscale');
        $settings = $this->getAttributeKeySettings();

        $valueObject = $this->getAttributeValue();
        $value = $valueObject ? (string) $valueObject->getValue() : '';
        $this->set('type', $settings->getType());
        $this->set('allowInvalidValues', $settings->isAllowInvalidValues());
        $this->set('value', $value);
    }

    public function composer()
    {
        $this->form();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::createAttributeValue()
     */
    public function createAttributeValue($value)
    {
        $checker = $this->app->make(Checker::class);
        $value = $checker->normalize($value);
        if ($value !== '') {
            $settings = $this->getAttributeKeySettings();
            if ($settings->isAllowInvalidValues() === false) {
                $valueType = $checker->getType($value);
                switch ($settings->getType()) {
                    case Checker::TYPE_VATCODE:
                        if ($valueType !== Checker::TYPE_VATCODE) {
                            throw new UserMessageException(t('The specified value is not a valid VAT code.'));
                        }
                        break;
                    case Checker::TYPE_CODICEFISCALE:
                        if ($valueType !== Checker::TYPE_CODICEFISCALE) {
                            throw new UserMessageException(t('The specified value is not a valid codice fiscale.'));
                        }
                        break;
                    default:
                        if ($valueType === '') {
                            throw new UserMessageException(t('The specified value is neither a valid VAT code nor a valid codice fiscale.'));
                        }
                        break;
                }
            }
        }

        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::exportKey()
     */
    public function exportKey($akey)
    {
        $settings = $this->getAttributeKeySettings();
        $type = $akey->addChild('type');
        $type->addAttribute('type', $settings->getType());
        $type->addAttribute('allow-invalid-values', $settings->isAllowInvalidValues() ? '1' : '0');

        return $akey;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::importKey()
     */
    public function importKey(SimpleXMLElement $akey)
    {
        $settings = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            if (isset($akey->type['type'])) {
                $settings->setType((string) $akey->type['type']);
            }
            if (isset($akey->type['allow-invalid-values'])) {
                $settings->setAllowInvalidValues(filter_var((string) $akey->type['allow-invalid-values'], FILTER_VALIDATE_BOOLEAN));
            }
        }

        return $settings;
    }
}
