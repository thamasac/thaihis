<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTemplate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row ezmodule-template-form">
  <div class="col-md-8">
  <?php 
  if((Yii::$app->user->can('administrator')) || $model['created_by']==$userId){?>
    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
      <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Template')?> <small>(for advanced users)</small></h4>
    </div>

    <div class="modal-body">
	<?php
        $settings = [
            'minHeight' => 500,
            'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
            'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
            'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
            'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
            'plugins' => [
                'fontcolor',
                'fontfamily',
                'fontsize',
                'textdirection',
                'textexpander',
                'counter',
                'table',
                'definedlinks',
                'video',
                'imagemanager',
                'filemanager',
                'limiter',
                'fullscreen',
                
            ],
            'paragraphize'=>false,
            'replaceDivs'=>false,
        ];
//        $lang = Yii::$app->language;
//        if ($lang != 'en-US') {
//            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
//        }
        ?>
        
	<?= $form->field($model, 'template_name')->textInput(['maxlength' => true]) ?>
        
<!--        <div class="form-group pull-right">
            <a id="modal-addbtn-ezmodule-widget" style="cursor: pointer;" class="btn btn-danger btn-xs" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/list'])?>"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></a>
        
        </div>-->
        
        <?php
        echo $form->field($model, 'template_html')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
            'clientOptions' => [
                'zIndex' => 1000,
            ]
        ]);

        ?>
        <?=
            $form->field($model, 'template_js')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'javascript', // programing language mode. Default "html"
                'id' => 'template_js'
            ]);
            ?>
        <?=
            $form->field($model, 'template_css')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'css', // programing language mode. Default "html"
                'id' => 'template_css'
            ]);
            ?>
	<?= $form->field($model, 'public')->checkbox() ?>
        
        <?php
        if (Yii::$app->user->can('administrator')) {
            echo $form->field($model, 'template_system')->checkbox();
        } else {
            echo $form->field($model, 'template_system')->hiddenInput()->label(FALSE);
        }
        ?>
        
	<?= $form->field($model, 'sitecode')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

    </div>
    <div class="modal-footer">
      
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : '<i class="fa fa-pencil"></i> '.Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <a id="modal-del-template" style="cursor: pointer;" class="btn btn-danger" data-url="<?= Url::to(['/ezmodules/ezmodule-template/delete', 'id'=>$model->template_id])?>"><i class="fa fa-trash"></i> <?= Yii::t('app', 'Delete')?></a>
        
    </div>
  
    <?php ActiveForm::end(); ?>
  <?php } else { ?>
  <div class="alert alert-danger">
      <?= Yii::t('ezmodule', 'You do not have permission to edit this module.')?>
  </div>
  <?php } ?>
  </div>
  <div class="col-md-4 sdbox-col">
      <div id="modal-ezmodule-widget-box" class="ezmodule-widget-index">

        <?php  Pjax::begin(['id'=>'ezmodule-widget-grid-pjax']);?>
        <div class="modal-header">
          <div class="pull-right"><?=Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['/ezmodules/ezmodule-widget/create', 'ezm_id'=>$module]), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-ezmodule-widget'])?></div>
             <h4 class="modal-title" id="itemModalLabel"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></h4>
         </div>
         <div class="modal-body">
             <?= GridView::widget([
             'id' => 'ezmodule-widget-grid',
             'dataProvider' => $dataProvider,
             'layout' => '{items}',    
             'columns' => [
                 [
                     'attribute'=>'widget_varname',
                     'value'=>function ($data){ return Html::button("{{$data['widget_varname']}}", ['class'=>'btn btn-warning btn-widget', 'data-widget'=>"{{$data['widget_varname']}}", 'data-dismiss'=>'modal']); },
                     'format'=>'raw',        
                     //'contentOptions'=>['style'=>'width:200px; '],
                 ],
                 [
                     'attribute'=>'widget_name',
                     'format'=>'raw', 
                     'value'=>function ($data){ 
                         return $data['widget_name'] .' <a data-toggle="tooltip" title="'.Html::encode($data['widget_detail']).'"><i class="glyphicon glyphicon-info-sign" ></i></a>'; 

                     },
                     //'contentOptions'=>['style'=>'width:250px; '],
                 ],
                 //'widget_detail:ntext',
                 'widget_example:ntext',
                 [
                    'class' => 'appxq\sdii\widgets\ActionColumn',
                    'contentOptions' => ['style'=>'width:80px;text-align: center;'],
                    'template' => '<p style="margin-bottom: 4px;">{update}</p>{delete}',
                     'buttons' => [
                        'update' => function ($url, $data, $key) {
                         if(((Yii::$app->user->can('administrator')) || $data['created_by']== Yii::$app->user->id) && $data['widget_type']!='core'){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Update'), Url::to(['/ezmodules/ezmodule-widget/update',
                                            'id' => $data->widget_id,
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
        <?php  Pjax::end();?>
     </div>
  </div>
</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>
<?php  
$this->registerJs("
 

$('#modal-ezmodule-widget-box').on('click', '.btn-widget', function() {
    
    $('#ezmoduletemplate-template_html').froalaEditor('html.insert', $(this).attr('data-widget'), false);
});

$('#modal-del-template').on('click', function() {
    var url = $(this).attr('data-url');
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                ". SDNoty::show('result.message', 'result.status') ."
                window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/template', 'id' => $module])."';
            } else {
                ". SDNoty::show('result.message', 'result.status') ."
            }
        }).fail(function() {
            ". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
            console.log('server error');
        });
    });
});


$('#ezmodule-widget-grid-pjax').on('click', '#modal-addbtn-ezmodule-widget', function() {
    modalEzmoduleWidget($(this).attr('data-url'));
});

$('#ezmodule-widget-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzmoduleWidget(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezmodule-widget-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
    }
    return false;
});

function modalEzmoduleWidget(url) {
    $('#modal-ezmodule-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-widget').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>