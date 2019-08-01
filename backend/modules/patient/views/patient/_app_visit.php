<?php
$reloadDiv = \appxq\sdii\utils\SDUtility::getMillisecTime();

if (!$result || empty($result)) {
    $url = yii\helpers\Url::to(['/patient/patient/appoint-visit', 'pt_id' => $pt_id, 'status' => 'save', 'options' => $options]);
    echo \yii\helpers\Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('patient', 'Add Visit'), '#'
        , ['class' => 'btn btn-success btn-save-visit', 'data-url' => $url]);
    if (empty($date) && $status == 'save') {
        echo \yii\helpers\Html::tag('p', 'โปรดระบุวันที่นัด', ['class' => 'help-block help-block-error']);
    }
    $alert = "        
         $('.btn-save-visit').on('click', function() {  
            let date = $('#ez1506908933027139000-app_date').val();
            let app_id = $('#ezform-1506908933027139000').attr('data-dataid');
            $.get('$url',{date:date,app_id:app_id}).done(function(result) {                
                $('div[item-id=\"1518957306070503900\"]').html(result);            
            });
            return false;
        });
        ";
} else {
    //ยิงไป doctor Order
    echo \appxq\sdii\widgets\ModalForm::widget([
        'id' => 'modal-order-appont',
        'size' => 'modal-xxl',
        'tabindexEnable' => false,
    ]);
    $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
    $url = yii\helpers\Url::to(['/thaihis/order/orderpopup', 'target' => $pt_id, 'visitid' => $result['id'], 'reloadDiv' => '', 'visit_type' => $result['visit_type'], 'pt_id' => $pt_id, 'options' => $options]);
    if ($options != '') {
        echo \yii\helpers\Html::button('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('patient', 'Add Order')
            , ['class' => 'btn btn-success btn-sm ezform-main-open', 'data-url' => $url, 'data-modal' => 'modal-' . $ezf_id]);
    }
    echo \yii\helpers\Html::tag('div', '', ['class' => 'clearfix']) . "<br/>";
//    echo backend\modules\patient\classes\PatientHelper::uiGridOrder($ezf_id, $result['id'], 'view-order-appoint',false,$options);
    echo backend\modules\thaihis\classes\ThaiHisHelper::uiGridOrder($ezf_id, $result['id'], 'view-order-appoint-' . $reloadDiv, false, \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($options));

    $alert = "
        
         $('#ez1506908933027139000-app_visit_id').val('{$result['id']}');
        
        var hasMyModal = $('body').has('#modal-{$ezf_id}').length;
        if(!hasMyModal){
             $('#ezf-modal-box').append('<div id=\"modal-{$ezf_id}\" class=\"fade modal\" role=\"dialog\"><div class=\"modal-dialog modal-xxl\"><div class=\"modal-content\"></div></div></div>');
        }
        
        $('#modal-1504537671028647300').on('hidden.bs.modal', function (e) {
            let url = $('#view-order-appoint-{$reloadDiv}').attr('data-url');
            if(typeof url != \"undefined\"){
                getUiAjax(url, 'view-order-appoint-{$reloadDiv}');
                $('#modal-1504537671028647300 .modal-content').html('');
            }
        });
        ";
}
$this->registerJS("
  $alert
  ");

?>