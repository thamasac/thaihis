<?php

$label = '';
$label .= $btn_icon != '' ? ' <i class="fa '.$btn_icon.'"></i> ' : ' <i class="fa fa-sign-out"></i> ';
$label .= $btn_text != '' ? $btn_text : ' Close Visit ';
$class = 'btn ';
$class .= $btn_color != '' ? $btn_color : ' btn-danger ';

if ($data['visit_tran_status'] == '1') {
    $initdata = ['visit_tran_close_type' => '1', 'visit_tran_status' => '2',];
    echo backend\modules\ezforms2\classes\BtnBuilder::btn()
        ->ezf_id($ezf_id)
        ->initdata($initdata)
        ->reloadDiv($reloadDiv)
        ->label($label)->options(['class' => $class.' '.$btn_style])
        ->buildBtnEdit($visit_tran_id);
}

\richardfan\widget\JSRegister::begin();
?>
<script>
    $('#ezform-<?=$ezf_id?>').on('beforeSubmit',function(){
        onLoadBlock('body');
    });

    function onLoadBlock(ele){
        $(ele).waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.8)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadBlock(ele){
        $(ele).waitMe("hide");
    }


    if ('<?=$data['visit_tran_status']?>' == '2') {
        onLoadBlock('body');
        window.location.href  = '<?=$current_url?>';
    }
</script>
<?php \richardfan\widget\JSRegister::end()?>

