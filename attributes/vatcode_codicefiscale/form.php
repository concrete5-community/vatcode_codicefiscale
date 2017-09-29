<?php

/* @var string $type */
/* @var string $value */

echo $form->text(
    $view->controller->field('value'),
    $value,
    [
        'class' => 'pkg-',
    ]
);
