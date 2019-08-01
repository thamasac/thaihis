<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$this->title = Yii::t('ezform', 'Restore EzForm');

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezforms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="pull-right" >
    <?= Html::a('<i class="fa fa-mail-reply"></i> ' . Yii::t('ezform', 'Back to form page'), ['/ezforms2/ezform/index'], ['class' => 'btn btn-default btn-flat']) ?>
</div>
<div class="sdbox-header" style="margin-bottom: 15px;">
  <h3><?=$this->title?> <small><?= Yii::t('ezform', 'Import a backup file containing EzForm stucture.')?></small></h3>
</div>
<div class="user-list-import">
    <div class="sdbox-content sdborder" style="padding: 15px;">
	 <?php $form = ActiveForm::begin([
	    'id'=>'import-form',
	    'options'=>['enctype'=>'multipart/form-data'],
	]); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= kartik\file\FileInput::widget([
                        'name'=>'excel_file',
                        'pluginOptions' => [
                            'previewFileType' => 'any',
                            'overwriteInitial' => true,
                            'showPreview' => true,
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
		<?= Html::submitButton('<i class="glyphicon glyphicon-import"></i> '.Yii::t('ezform', 'Restore EzForm'), ['class' => 'btn btn-info']) ?>
	    </div>
	    
	<?php ActiveForm::end(); ?>
    </div>
    
    <?php if(!empty($sum)):?>
	<div class="row" style="font-size: 18px;">
            <?php foreach ($sum as $key => $value):?>
	    <div class="col-md-3">
                <h4><?=$key?></h4>
		<table class="table table-striped">
		    <tbody>
			<tr>
			    <th><?=Yii::t('app', 'Complete')?></th>
			    <td style="text-align: right;"><code><?=number_format($value['tsum'])?></code></td>
			</tr>
			<tr>
			    <th><?=Yii::t('app', 'Failed')?></th>
			    <td style="text-align: right;"><code><?=number_format($value['fsum'])?></code></td>
			</tr>
                        <tr>
			    <th><?=Yii::t('yii', 'Error')?></th>
			    <td style="text-align: right;"><code><?=number_format($value['esum'])?></code></td>
			</tr>
			<tr>
			    <th><?=Yii::t('app', 'Total')?></th>
			    <td style="text-align: right;"><code><?=number_format($value['all'])?></code></td>
			</tr>
		    </tbody>
		</table>
	    </div>
            <?php endforeach;?>
	</div>
    <?php endif;?>
    <br>
    <br>
</div>
