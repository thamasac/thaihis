<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformCommunitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezform', 'Query Tool');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezform-community-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'ezform-community-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezform-community-grid',
	'panelBtn' => '',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            'ezf_name',
            'dataid',
            [
    'attribute' => 'created_at',
    'label' => Yii::t('ezform', 'Created At'),
    'value' => function ($data) {
        return !empty($data['created_at']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['created_at']) : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
    'filter' => '',
],
            [
    'header' => 'Request',
    'format' => 'raw',
    'value' => function ($data) {
        $count_field = \backend\modules\ezforms2\models\EzformCommunity::find()
                    ->where('object_id=:object_id AND dataid=:dataid AND type="query_tool"  AND parent_id=0', [':object_id'=>$data['object_id'], ':dataid'=>$data['dataid']])
                    ->count();
        return "<code>$count_field</code>";
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
],
            [
    'header' => 'Action',
    'format' => 'raw',
    'value' => function ($data) {
        return '<a href="'.Url::to(['/ezforms2/ezform-community/view', 'object_id'=>$data['object_id'], 'dataid'=>$data['dataid'], 'ezf_name'=>$data['ezf_name']]).'" class="btn btn-info btn-xs btn-views">Views</a>';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
],
            // 'dataid',
            // 'content:ntext',
            // 'query_tool',
            // 'field',
            // 'value_old:ntext',
            // 'value_new:ntext',
            // 'approv_by',
            // 'approv_date',
            // 'approv_status',
            // 'status',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

//	    [
//		'class' => 'appxq\sdii\widgets\ActionColumn',
//		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
//		'template' => '{view} {update} {delete}',
//	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>
  
</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezform-community',
    'size'=>'modal-xxl',
]);
?>

<?php  $this->registerJs("

$('#ezform-community-grid-pjax').on('click', '.btn-views', function(){
var url = $(this).attr('href');
modalEzformCommunity(url);
return false;
});

function modalEzformCommunity(url) {
    $('#modal-ezform-community .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-community').modal('show')
    .find('.modal-content')
    .load(url);
}
");?>