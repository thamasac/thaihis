<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = Yii::t('app', 'EzForm Question Types');

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sdbox-header" style="margin-bottom: 15px;">
  <div class="pull-right">
            <?= Html::a('<i class="fa fa-mail-reply"></i> ' . Yii::t('ezform', 'Back to form page'), ['/ezforms2/ezform/index'], ['class' => 'btn btn-default btn-flat']) ?>
        </div>
    <h3><?= Html::encode($this->title) ?></h3>
</div>

<?php
    echo GridView::widget([
        'id' => 'input-help-grid',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'input_icon',
                'format' => 'raw',
                'value' => function ($data) {
                    $url = $data['input_base_url'].'/';

                    $src = Yii::getAlias('@storageUrl/images/icon_empty.png');
                    if(isset($data['input_icon'])){
                        $src = $url . $data['input_icon'];
                    }
                    
                    return \yii\helpers\Html::img($src, ['class'=>'img-rounded', 'width'=>30]);
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'max-width:60px;width:60px;text-align: center;'],
            ],
            [
                'attribute' => 'input_name',
            ],
            [
                'attribute' => 'input_category',
                'value' => function ($data) {
                    if(isset($data['input_category'])){
                        return \backend\modules\core\classes\CoreFunc::itemAlias('input_category', $data['input_category']);
                    } else {
                        return Yii::t('ezform', 'None');
                    }
                    
                },
                'contentOptions' => ['style' => 'width:150px;'],
            ],
            [
                'attribute' => 'content',
                'header' => Yii::t('ezform', 'Help'),
                'format' => 'raw',
                'value' => function ($data) {
                    $Html = \yii\helpers\Html::tag('div', $data['content'], ['style'=>'display: none;', 'id'=>'content-'.$data['input_id']]);
                    return $Html . \yii\helpers\Html::a('<i class="glyphicon glyphicon-info-sign"></i>', '', ['class'=>'btn btn-default btn-inputhelp', 'data-content'=>'content-'.$data['input_id']]);
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:60px;max-width:60px;text-align: center;'],
            ],
            [
                'attribute' => 'input_link',
                'header' => Yii::t('ezform', 'VDO'),
                'format' => 'raw',
                'value' => function ($data) {
                    $url = isset($data['input_link'])?$data['input_link']:'#';
                    return \yii\helpers\Html::a('<i class="glyphicon glyphicon-film"></i>', $url, ['class'=>'btn btn-default', 'target'=>'_blank']);
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:60px;max-width:60px;text-align: center;'],
            ],
        ],
    ]);
                
yii\bootstrap\Modal::begin([
        'header' => '<h4 class="modal-title">'.Yii::t('ezform', 'Help').'</h4>',
        'size' => 'modal-lg',
        'options'=>['id'=>'modal-info']
    ]);

echo \yii\helpers\Html::tag('div', '', ['id'=>'info-box']);

yii\bootstrap\Modal::end();
    ?>


<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('.btn-inputhelp').click(function(){
        $('#modal-info .modal-content .modal-body #info-box').html($('#'+$(this).attr('data-content')).html());
        $('#modal-info').modal('show');
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>