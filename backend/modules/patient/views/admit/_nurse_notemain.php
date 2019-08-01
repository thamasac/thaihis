<?php

echo backend\modules\ezforms2\classes\BtnBuilder::btn()
        ->ezf_id($ezf_id)
        ->initdata(['visit_tran_close_type' => '1'])
        ->reloadDiv($reloadDiv)
        ->label('<i class="fa fa-sign-out"></i> Close Visit')->options(['class' => 'btn btn-danger btn-block'])
        ->buildBtnEdit($visit_tran_id);
?>
