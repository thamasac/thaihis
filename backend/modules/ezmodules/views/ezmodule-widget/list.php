<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\grid\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleWidgetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezmodule', 'Widget');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezmodule-widget-index">

   <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></h4>
    </div>
    <div class="modal-body">
      <?php if(isset($ezm_id) && $ezm_id>0){?>
      <div class="pull-right" style="margin-bottom: 15px;">
          <?=Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['/ezmodules/ezmodule-widget/create', 'ezm_id'=>$ezm_id, 'modal'=>1]), 'class' => 'btn btn-success ', 'id'=>'modal-addbtn-widget'])?>
      </div>
      <div style="margin-bottom: 15px;width: 50%;" >
        <div class="input-group">
           <?php
          
          echo \kartik\select2\Select2::widget([
                'id'=>'widget_temp_id',
                'name' => 'widget_temp',
                'options' => ['placeholder' => 'Choose to clone Widget ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezmodules/ezmodule-widget/get-my-widget']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
                'pluginEvents' => [
                    "select2:select" => "function(e) { $('#btn-clone-widget').show(); }",
                    "select2:unselect" => "function() { $('#btn-clone-widget').hide(); }"
                ]
            ]);
          ?>
          <span class="input-group-btn">
            <button class="btn btn-success" id="btn-clone-widget" style="display: none;"><?=SDHtml::getBtnAdd().' '.Yii::t('ezform', 'Clone')?></button>
          </span>
      </div>
         
      </div>
      
      <?php } ?>
        <?= GridView::widget([
	'id' => 'ezmodule-widget-grid',
	'dataProvider' => $dataProvider,
        'layout' => '{items}',    
        'columns' => [
            [
		'attribute'=>'widget_varname',
		'value'=>function ($data){ return Html::button($data['widget_varname'], ['class'=>'btn btn-warning btn-widget', 'data-widget'=>"{{$data['widget_varname']}}", 'data-dismiss'=>'modal']); },
                'format'=>'raw',        
		'contentOptions'=>['style'=>'width:120px; '],
            ],
            [
		'attribute'=>'widget_name',
                'format'=>'raw', 
                'value'=>function ($data){ 
                    return $data['widget_name'] .' <a data-toggle="tooltip" title="'.Html::encode($data['widget_detail']).'"><i class="glyphicon glyphicon-info-sign" ></i></a>'; 
                    
                },
		'contentOptions'=>['style'=>'width:180px; '],
            ],
            //'widget_detail:ntext',
            'widget_example:ntext',
            [
                'class' => 'appxq\sdii\widgets\ActionColumn',
                'contentOptions' => ['style'=>'width:180px;'],
                'template' => '{update} {delete}',
                 'buttons' => [
                    'update' => function ($url, $data, $key) {
                     if(((Yii::$app->user->can('administrator')) || $data['created_by']== Yii::$app->user->id) && $data['widget_type']!='core'){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Update'), Url::to(['/ezmodules/ezmodule-widget/update',
                                        'id' => $data->widget_id,
                                        'modal'=>1,
                                    ]), [
                                    'data-action' => 'update',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-primary btn-xs',
                        ]);
                     }
                    },
                    'delete' => function ($url, $data, $key) {
                        if(((Yii::$app->user->can('administrator')) || $data['created_by']== Yii::$app->user->id) && $data['widget_type']!='core'){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii', 'Delete'), Url::to(['/ezmodules/ezmodule-widget/delete',
                                            'id' => $data->widget_id,
                                        ]), [
                                        'data-action' => 'delete',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-danger btn-xs',
                            ]);
                        }
                    },
                ],
            ],                
        ],
    ]); ?>
    </div>
    

</div>

<?php  

$this->registerJs("

$('#modal-addbtn-widget').on('click', function() {
    modalAddWidget($(this).attr('data-url'));
});

$('#btn-clone-widget').on('click', function() {
    let id = $('#widget_temp_id').val();
    $.ajax({
	    method: 'POST',
	    url:'".Url::to(['/ezmodules/ezmodule-widget/clone-widget', 'ezm_id'=>$ezm_id])."',
	    data: {id:id},
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
                    $('#modal-add-widget').modal('show')
                    .find('.modal-content')
                    .load('".Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$ezm_id])."');
		} else {
		}
                ". SDNoty::show('result.message', 'result.status') ."
	    }
    });
});

$('#ezmodule-widget-grid tbody tr td a').on('click', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalAddWidget(url);
        
        return false;
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    
                    $('#modal-add-widget').modal('show')
                    .find('.modal-content')
                    .load('".Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$ezm_id])."');
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
        return false;
    }
    
});

function modalAddWidget(url) {
    $('#modal-ezmodule-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-widget').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>