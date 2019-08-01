<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="row">
  <div class="row">
  <div class="col-md-6 col-md-offset-2">
    <ul class="nav nav-tabs">
  <li role="presentation" ><a href="<?= yii\helpers\Url::to(['/thaihis/default/configbku']) ?>">Backup</a></li>
  <li role="presentation" class="active"><a href="<?= yii\helpers\Url::to(['/thaihis/default/log-to-db']) ?>">Logs2DB</a></li>
</ul>
    <br>
  </div>
</div>
  
    <div class="col-md-6 col-md-offset-2">
        <?php $form = ActiveForm::begin([//post
            'id' => 'config_bku',
            'action' => Url::to('/thaihis/default/backup-to-db'),
        ]); ?>
        <!--    <div class="form-group">-->
        <!--      <label>Function (get data)</label>-->
        <!--      --><?php //echo Html::textInput('func', 'backend\modules\thaihis\classes\BackupFunc::getDeptAllTest($s, $e)', ['class'=>'form-control']);?>
        <!--    </div>-->

        <div class="form-group">
            <label>Table name</label>
            <!--            --><?php //echo Html::textInput('table_name', 'zdata_working_unit', ['class'=>'form-control']);?>
            <?php
            echo \kartik\select2\Select2::widget([
                'name' => 'table_name',
                'data' => \yii\helpers\ArrayHelper::map($data_form, 'id', 'name'),
                'pluginOptions' => [
                    'allowClear' => TRUE,
                ],
                'options' => ['id' => 'table_name', 'placeholder' => 'Function']
            ]);
            ?>
        </div>

        <div class="form-group">
            <label>Step</label>
            <?= Html::textInput('step', '2000', ['class' => 'form-control']); ?>
        </div>
        <!--    -->
        <!--    <div class="form-group">-->
        <!--      <label>initdata</label>-->
        <!--      --><?php //echo Html::textarea('initdata', '[]', ['class'=>'form-control']);?>
        <!--    </div>-->

        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        <br><br>
        <?php ActiveForm::end(); ?>
    </div>

  
  
</div>
