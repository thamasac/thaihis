<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\linebot\models\LineFunctions */

$this->title = 'Line Functions#'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('linebot', 'Line Functions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-functions-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
      <?php
      $attr = [];
      if($show=='all'){
          $attr = [
		'id',
		'channel_id',
		'command',
		[
                'attribute' => 'api',
                'format'=>'raw',
                'value' => \yii\helpers\VarDumper::dumpAsString(\appxq\sdii\utils\SDUtility::string2Array($model['api']), 10, true), 
            ],
              [
                'attribute' => 'template',
                'format'=>'raw',
                'value' => \yii\helpers\VarDumper::dumpAsString(\appxq\sdii\utils\SDUtility::string2Array($model['template']), 10, true), 
            ],
              [
                'attribute' => 'options',
                'format'=>'raw',
                'value' => \yii\helpers\VarDumper::dumpAsString(\appxq\sdii\utils\SDUtility::string2Array($model['options']), 10, true), 
            ],
              [
                'attribute' => 'role',
                'format'=>'raw',
                'value' => \yii\helpers\VarDumper::dumpAsString(\appxq\sdii\utils\SDUtility::string2Array($model['role']), 10, true), 
            ],
		'active',
		'updated_by',
		'updated_at',
		'created_by',
		'created_at',
	    ];
      } else {
        $attr = [
            [
                'attribute' => $show,
                'format'=>'raw',
                'value' => \yii\helpers\VarDumper::dumpAsString(\appxq\sdii\utils\SDUtility::string2Array($model[$show]), 10, true), 
            ],
        ];
      }
      ?>
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => $attr,
	]) ?>
    </div>
</div>
