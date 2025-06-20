<?php

use VatcodeCodicefiscale\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $type
 * @var bool $allowInvalidValues
 */

?>
<fieldset>
    <legend><?= t('VAT code / Codice Fiscale Options') ?></legend>
    <div class="form-group">
        <?= $form->label('type', t('Value type')) ?>
        <?= $form->select(
            'type',
            [
                '' => t('VAT Code or Codice Fiscale'),
                Checker::TYPE_VATCODE => t('VAT Code'),
                Checker::TYPE_CODICEFISCALE => t('Codice Fiscale'),
            ],
            $type
        ) ?>
    </div>
    <div class="form-group">
        <div class="checkbox">
            <label class="checkbox">
                <?= $form->checkbox(
                    'allowInvalidValues',
                    '1',
                    $allowInvalidValues
                ) ?>
                <?= t('Accept invalid values') ?>
            </label>
        </div>
    </div>
</fieldset>
