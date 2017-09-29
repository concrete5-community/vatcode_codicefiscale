<fieldset>
    <legend><?= t('VAT code / Codice Fiscale Options') ?></legend>
    <div class="form-group">
        <?= $form->label('type', t('Value type')) ?>
        <?= $form->select(
	        'type',
	        [
                '' => t('VAT Code or Codice Fiscale'),
	            'vatcode' => t('VAT Code'),
	            'codicefiscale' => t('Codice Fiscale'),
	        ]
	    ) ?>
	</div>
</fieldset>
