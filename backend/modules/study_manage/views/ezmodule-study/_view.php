<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerCssFile("@web/css/checkbox-style.css?2");
$column = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:60px;text-align: center;'],
    ],
    [
        'attribute' => 'ezm_icon',
        'value' => function ($data) {
            if (isset($data['ezm_icon']) && !empty($data['ezm_icon'])) {
                return Html::img(Yii::getAlias('@storageUrl/module') . '/' . $data['ezm_icon'], ['width' => 25, 'class' => 'img-rounded']);
            } else {
                return Html::img(ModuleFunc::getNoIconModule(), ['width' => 25, 'class' => 'img-rounded']);
            }
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '5%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
    'ezm_name',
    [
        'attribute' => 'ezm_order',
        'header' => 'Order',
        'value' => function ($data) {
            $ezm_data = backend\modules\study_manage\classes\StudyQuery::getModuleStudyTemplates($data['ezm_id']);
            return Html::input('number', '',$ezm_data['ezm_order'],['class'=>'form-control order_module','data-ezm_id'=>$data['ezm_id']]);
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '100px'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
    [
        'header' => 'Add/Remove',
        'value' => function ($data) {
            $study_check = backend\modules\study_manage\classes\StudyQuery::getModuleStudyTemplates($data['ezm_id']);
            if($study_check){
                return '<div class="checkbox1 checkbox1-success">'.
                            Html::checkbox('checked'.$data['ezm_id'], true,['class'=>'check_module','id'=>'checked'.$data['ezm_id'],'data-ezm_id'=>$data['ezm_id']]).
                            Html::label('', 'checked'.$data['ezm_id']).
                        '</div>';
                
            }else{
                return '<div class="checkbox1 checkbox1-success">'.
                            Html::checkbox('checked'.$data['ezm_id'], false,['class'=>'check_module','id'=>'checked'.$data['ezm_id'],'data-ezm_id'=>$data['ezm_id']]).
                            Html::label('',  'checked'.$data['ezm_id']).
                        '</div>';
            }
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '5%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
];

$user_id = Yii::$app->user->id;
$template = \backend\modules\ezmodules\classes\ModuleQuery::getTemplate($user_id);
?>
<div class="modal-header">
    <h5 class="modal-title pull-left">Ezmodule All</h5>
    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="ezmodule-index" >

        <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
        <?php backend\modules\ezforms2\classes\EzfStarterWidget::begin() ?>
        <?php Pjax::begin(['id' => 'ezmodule-grid-pjax']); ?>
        <?=
        GridView::widget([
            'id' => 'ezmodule-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $column,
        ]);
        ?>
        <?php Pjax::end(); ?>
        <?php backend\modules\ezforms2\classes\EzfStarterWidget::end() ?>
    </div>

    <?=
    ModalForm::widget([
        'id' => 'modal-ezmodule',
        'size' => 'modal-lg',
        'tabindexEnable' => FALSE,
    ]);
    ?>
</div>
<div class="modal-footer">
    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
        Close
    </button>
</div>
<?php $this->registerJs("

"); ?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.check_module').on('change',function(){
        var ezm_id = $(this).attr('data-ezm_id');
        var check = $(this).is(':checked');
        var url = '/study_manage/ezmodule-study/update-study-template';
        $.get(url,{ezm_id:ezm_id,checked:check},function(data){
            
        });
    });
    
    $('.order_module').on('change',function(){
        var ezm_id = $(this).attr('data-ezm_id');
        var order = $(this).val();
        var url = '/study_manage/ezmodule-study/update-module-order';
        $.get(url,{ezm_id:ezm_id,order_module:order},function(data){
            
        });
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>