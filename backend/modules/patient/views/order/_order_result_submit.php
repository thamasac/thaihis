<?php

if ($dept <> 'C') {
    $script = " $('#modal-order-counter').modal('hide');
               $.pjax.reload({container: '#{$reloadDiv}'});
             ";
} else {
    $script = "
        $('form#order-receive').submit();
        ";
}
$this->registerJS($script);
?>