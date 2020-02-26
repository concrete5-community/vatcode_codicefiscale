<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Attribute\View $view */
/* @var string $type */
/* @var bool $allowInvalidValues */
/* @var string $value */

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
$(document).ready(function() {
    var $i = $('input[data-vatcode-codicefiscale-type]');
    $i.vatcodeCodicefiscale({
        onCheck: function ($input, rc) {
            var $parent = $input.closest('.control-group');
            $parent.removeClass('has-error has-success');
            if (rc === true) {
                $parent.addClass('has-success');
            } else if (rc === false) {
                $parent.addClass('has-error');
            }
        }
    });
});
</script>