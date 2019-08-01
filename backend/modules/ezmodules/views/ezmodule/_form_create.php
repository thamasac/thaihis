<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use yii\widgets\ListView;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><i class="fa fa fa-cube"></i> <?= Yii::t('ezmodule', 'Create New Module')?></h4>
</div>
<div class="modal-body">
  
  <div id="view-method">
  <label><i class="fa fa-clone"></i> <?= Yii::t('ezmodule', 'Clone from existing Module templates')?></label><br>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li role="presentation" class="active"><a href="#system" aria-controls="system" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'System Templates')?></a></li>
    <li role="presentation"><a href="#user" aria-controls="user" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'User\'s Generated Templates')?></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="system">
        <div id="template-box">
      <?=
        ListView::widget([
            'id'=>'ezf_items',
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'layout'=>'<div class="  text-right" >{summary}</div><div class="row" style="margin-top: 15px;">{items}</div><div class="list-pager">{pager}</div>',
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_ezmodule_item', [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                ]);
            },
        ])
        ?>
  </div>
    </div>
      <div role="tabpanel" class="tab-pane" id="user">
          <div id="template-user-box">
            <div class="alert alert-warning"><?= Yii::t('app', 'No results found.')?></div>
        </div>
    </div>
   
  </div>
  
  
  <hr>
  <label><?=Yii::t('ezmodule', 'Or Create from scratch')?> </label><br>
    <?php echo Html::button(SDHtml::getBtnAdd() . ' '. Yii::t('ezmodule', 'Create New EzModule'), ['data-url' => Url::to(['/ezmodules/ezmodule/save']), 'class' => 'btn btn-success', 'id' => 'modal-step-ezmodule']);?>
  <hr>
  <label><?=Yii::t('ezmodule', 'Or Restore from a Module Backup file')?> </label>
<div class="user-list-import">
	 <?php $form = ActiveForm::begin([
	    'id'=>'import-form',
	    'options'=>['enctype'=>'multipart/form-data'],
	]); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= \appxq\sdii\widgets\FileInput::widget([
                        'id'=>'excel_file',
                        'name'=>'excel_file',
                        'pluginOptions' => [
                            'previewFileType' => 'any',
                            'overwriteInitial' => true,
                            'showPreview' => FALSE,
                            'showCaption' => true,
                            'showRemove' => FALSE,
                            'showUpload' => FALSE,
                            'allowedFileExtensions' => ['xls', 'xlsx', 'xlsm', 'xlsb', 'csv'],
                            'maxFileSize' => 5000,
                        ]
                    ])?>
                </div>
            </div>
        </div>
	    
	    
        <div class="form-group">
            <?= Html::submitButton('<i class="glyphicon glyphicon-import"></i> '.Yii::t('ezmodule', 'Create Module a backup file'), ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>
    
  <div id="sum_import"></div>
    
    </div>
  </div>
  <div id="view-form" style="display: none;">
    <ul class="breadcrumb">
      <li><a href="#" class="btn-back" style="text-decoration: none;"><?= Yii::t('ezmodule', 'Back to create new EzModule')?></a></li>
        <li class="active"><?= Yii::t('ezmodule', 'Edit EzModule')?></li>
    </ul>
    <div class="content"></div>
  </div>
  
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('#ezf_items .pagination a').on('click', function() {
        $.ajax({
            method: 'POST',
            url: $(this).attr('href'),
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#modal-create .modal-content').html(result);
            }
        });
        return false;
    });

    $('form#import-form').on('beforeSubmit', function(e) {
        var $form = $(this);
        var formData = new FormData($(this)[0]);

        $.ajax({
          url: $form.attr('action'),
          type: 'POST',
          data: formData,
	  dataType: 'JSON',
	  enctype: 'multipart/form-data',
	  processData: false,  // tell jQuery not to process the data
	  contentType: false,   // tell jQuery not to set contentType
          success: function (result) {
	    if(result.status == 'success') {
                $('#sum_import').html(result.html);
                //$.pjax.reload({container:'#ezmodule-grid-pjax',timeout: false});
                reloadNow = 1;
                $('#modal-create #view-method').hide();
                $('#modal-create #view-form').show();
                $('#modal-create #view-form .content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');

                $.ajax({
                    url:'<?= Url::to(['/ezmodules/ezmodule/save'])?>',
                    method: 'GET',
                    data:{id:result.id},
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#modal-create #view-form .content').html(result);
                    }
                });
            } else {
                $('#sum_import').html('');
            } 
            <?=SDNoty::show('result.message', 'result.status')?>
          },
          error: function () {
            <?=SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
          }
      });
      return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>