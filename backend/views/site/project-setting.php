<?php

use backend\modules\ezforms2\classes\BtnBuilder;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
$modal = "modal-setting-project";
$ezf_id = '1523071255006806900';
$myproject = isset(Yii::$app->params['my_project'])&&!empty(Yii::$app->params['my_project'])?Yii::$app->params['my_project']:'';
//\appxq\sdii\utils\VarDumper::dump($myproject);
$data_id = isset($myproject['data_dynamic']['data_id'])&&!empty($myproject['data_dynamic']['data_id'])?$myproject['data_dynamic']['data_id']:'';
try{
    $table = 'zdata_create_project';
    $dbName = isset($myproject['data_dynamic']['dbname'])&&$myproject['data_dynamic']['dbname'] != ''?$myproject['data_dynamic']['dbname']:'';
    $sql = "REPLACE INTO {$dbName}.{$table} (SELECT * FROM {$table} WHERE id='{$data_id}')";
    \Yii::$app->db_main->createCommand($sql)->execute();
} catch (\yii\db\Exception $ex) {
    backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
}
//\appxq\sdii\utils\VarDumper::dump($data_id);
?>
<div class="alert alert-warning" style="margin-top:10px;font-size:80px;text-align: center;padding:100px;"><i class="fa fa-wrench" aria-hidden="true"></i> <?= Yii::t('app','Project Setting')?></div>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin()?>
<?php
echo BtnBuilder::btn()
        ->ezf_id($ezf_id)
        ->dataid($data_id)
        ->options(['data-id' => $data_id, 'class' => 'btn btn-success btn-block btn-edit-form-setting hidden'])
        ->modal($modal)
//        ->reloadDiv($reloadDiv)
        ->label('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'))
        ->buildBtnAdd();
?>

<?=\appxq\sdii\widgets\ModalForm::widget([
    'id' => $modal,
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    init_form_setting=function(){
        setTimeout(function(){
            $( ".btn-edit-form-setting" ).trigger( "click" );
        },500);
    }
    init_form_setting();
    $(document).on('hide.bs.modal','#<?= $modal?>', function () {
        go_back();
        console.log('back to page');
    });
    
    function go_back() {
        window.history.back();
    }
    
</script>
<?php \richardfan\widget\JSRegister::end();?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::end();?>