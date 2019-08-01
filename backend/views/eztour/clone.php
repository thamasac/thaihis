<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    $options['datas'] = $options;
?>
<div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <span class="pull-right"> 
                    <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('tour','ADD'), ['class'=>'btn btn-sm btn-success btnAdd'])?>
                </span>
                <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
            </div>
            <div class="panel-body">
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Element ID') ?></label>
                    <?= Html::textInput('options[datas][element_id]', isset($options['datas']['element']) ? $options['datas']['element'] : '', ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Title') ?></label>
                    <?= Html::textInput('options[datas][title]', isset($options['datas']['title']) ? $options['datas']['title'] : '', ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Placement') ?></label>
                    <?php
                    $items = [
                        'auto' => 'Auto', 'top' => 'Top', 'left' => 'Left', 'bottom' => 'Bottom', 'right' => 'Right'
                    ];
                    ?>
                    <?= Html::dropDownList('options[datas][placement]', isset($options['datas']['placement']) ? $options['datas']['placement'] : '0', $items, ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-3">
                    <label><?= Yii::t('chanpan', 'smartPlacement') ?></label>
                    <?= Html::radioList('options[datas][smartPlacement]', isset($options['datas']['smartPlacement']) ? $options['datas']['smartPlacement'] : true, [true => 'TRUE', false => 'FALSE']); ?>
                </div>
                <div class="clearfix"></div><hr/>
                <div class="col-md-12">
                    <label><?= Yii::t('tour', 'Content') ?></label>
                    <?php
                    echo \appxq\sdii\widgets\FroalaEditorWidget::widget([
                        'name' => 'options[datas][content]',
                        'value' => isset($options['datas']['content']) ? $options['datas']['content'] : '',
                        'toolbar_size' => 'lg',
                        'options' => ['class' => 'eztemplate', 'id' => appxq\sdii\utils\SDUtility::getMillisecTime()],
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
