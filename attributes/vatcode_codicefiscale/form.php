<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Attribute\View $view
 * @var string $type
 * @var bool $allowInvalidValues
 * @var string $value
 */

echo $form->text(
    $view->controller->field('value'),
    $value,
    [
        'data-vatcode-codicefiscale-type' => $type,
        'data-vatcode-codicefiscale-allow-invalid-values' => $allowInvalidValues ? 'yes' : 'no',
    ]
);
?>
<script>
(function() {

function ready() {
    function onCheck(el, rc) {
        const parent = el.closest('.control-group');
        if (parent) {
            parent.classList.toggle('has-success', rc === true);
            parent.classList.toggle('has-error', rc === false);
        }
    }
    document.querySelectorAll('input[data-vatcode-codicefiscale-type]:not([data-vatcode-codicefiscale])').forEach((el) => {
        new window.ccmVatcodeCodicefiscale(el, {
            onCheck,
        });
    });

}
if (document.readyState !== 'loading') {
    ready();
} else {
    document.addEventListener('DOMContentLoaded', ready);
}

})();
</script>