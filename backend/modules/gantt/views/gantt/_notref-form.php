<?php 
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use backend\modules\ezforms2\classes\EzfFunc;
?>   

    <div class="notreff-ezform">
        <div class="form-group row">
            <div class="col-md-6 ">
                <?= Html::label(Yii::t('ezmodule', 'Start Date'), 'options[start_date]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('date', 'options[start_date]', '', ['class' => 'form-control']);
                ?>
            </div>
            <div class="col-md-6 sdbox-col">
                <?= Html::label(Yii::t('ezmodule', 'Progress'), 'options[progress]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[progress]', '', ['class' => 'form-control','step'=>'0.1','min'=>'0','max'=>'10']);
                ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6 ">
                <?= Html::label(Yii::t('ezmodule', 'Sort order'), 'options[sortorder]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[sortorder]', '', ['class' => 'form-control']);
                ?>
            </div>
            <div class="col-md-6 sdbox-col">

                <?= Html::label(Yii::t('ezform', 'Parent'), 'options[config_parent]', ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => 'options[config_parent]',
                    'value' => '',
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Parent'), 'id' => 'config_parent'],
                    'data' => $data_node,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>
