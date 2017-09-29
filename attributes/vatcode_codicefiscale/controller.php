<?php
namespace Concrete\Package\VatcodeCodicefiscale\Attribute\VatcodeCodicefiscale;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Concrete\Core\Error\UserMessageException;
use Concrete\Package\VatcodeCodicefiscale\Checker;
use Concrete\Package\VatcodeCodicefiscale\Entity\Attribute\Key\Settings\VatcodeCodicefiscaleSettings;
use SimpleXMLElement;

class Controller extends DefaultController
{
    public $helpers = ['form'];

    /**
     * {@inheritdoc}
     */
    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeKeySettingsClass()
    {
        return VatcodeCodicefiscaleSettings::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('id-card-o');
    }

    public function type_form()
    {
        $settings = $this->getAttributeKeySettings();
        $this->set('type', $settings->getType());
    }

    public function saveKey($data)
    {
        $data = (is_array($data) ? $data : []) + [
            'type' => '',
        ];
        $settings = $this->getAttributeKeySettings();
        switch ($data['type']) {
            case 'vatcode':
            case 'codicefiscale':
                $settings->setType($data['type']);
                break;
            default:
                $settings->setType('');
                break;
        }

        return $type;
    }

    public function getDisplayValue()
    {
        return parent::getDisplayValue();
    }

    public function form()
    {
        $this->requireAsset('vatcode_codicefiscale');
        $settings = $this->getAttributeKeySettings();

        $valueObject = $this->getAttributeValue();
        $value = $valueObject ? (string) $valueObject->getValue() : '';
        $this->set('type', $settings->getType());
        $this->set('value', $value);
    }

    public function composer()
    {
        $this->form();
    }

    public function searchForm($list)
    {
        $ak = $this->getAttributeKey();
        if ($ak) {
            $list->filterByAttribute($ak->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
        }

        return $list;
    }

    public function search()
    {
        $formHelper = $this->app->make('helper/form');
        echo $formHelper->text($this->field('value'), $this->request('value'));
    }

    public function createAttributeValue($value)
    {
        $checker = $this->app->make(Checker::class);
        /* @var Checker $checker */
        $value = $checker->normalize($value);
        if ($value !== '') {
            $valueType = $checker->getType($value);
            $settings = $this->getAttributeKeySettings();
            switch ($settings->getType()) {
                case 'vatcode':
                    if ($valueType !== Checker::TYPE_VATCODE) {
                        throw new UserMessageException(t('The specified value is not a valid VAT code.'));
                    }
                    break;
                case 'codicefiscale':
                    if ($valueType !== Checker::TYPE_CODICEFISCALE) {
                        throw new UserMessageException(t('The specified value is not a valid codice fiscale.'));
                    }
                    break;
                default:
                    if ($valueType === null) {
                        throw new UserMessageException(t('The specified value is neither a valid VAT code nor a valid codice fiscale.'));
                    }
                    break;
            }
        }

        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    public function exportKey($akey)
    {
        $settings = $this->getAttributeKeySettings();
        $type = $akey->addChild('type');
        $type->addAttribute('type', $settings->getType());

        return $akey;
    }

    public function importKey(SimpleXMLElement $akey)
    {
        $settings = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            if (isset($akey->type['type'])) {
                $settings->setType((string) $akey->type['type']);
            }
        }

        return $settings;
    }
}
