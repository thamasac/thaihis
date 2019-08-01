<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>

<?php
$items_role = (new yii\db\Query())->select(['role_name', 'CONCAT(role_detail,\' (\',role_name,\')\') as role_detail'])->from('zdata_role')->all();
$items_user = common\modules\user\models\Profile::find()->select(['user_id', 'CONCAT(firstname,\' \',lastname) as name'])->where('sitecode = :sitecode', [':sitecode' => Yii::$app->user->identity->profile->sitecode])->all();
$item_field = backend\modules\ezforms2\classes\EzfQuery::getFieldAllVersion($ezf_id);
foreach ($item_field as $key => $value) {
    if ($value['ezf_field_label']) {
        $item_field[$key]['ezf_field_label'] = $value['ezf_field_label'] . " (" . $value['ezf_field_name'] . ")";
    }
}
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
//\appxq\sdii\utils\VarDumper::dump($configAdvance);
if (!empty($configAdvance)) {

    foreach ($configAdvance['action_choice'] as $key => $value) {
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        ?>
        <div class="divMainAdvance-<?= $id ?>">

            <div class="form-group row notify"> 
                <hr>
                <div class="modal-header divHeadConfigMessage" style="background-color:#b9b5b8 ">
                <?= Html::button('<span class="glyphicon glyphicon-remove"></span>', ['class' => 'btn btn-danger btn-xs btn-remove-div pull-right']); ?>
                    <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Message') ?></h4>
                    
                </div>

                <div class="divConfigMessage">
                    <div class="col-md-3" style="margin-top:10px;">
                        <!--<div class="col-md-12 " style="margin-top:10px;">-->
                        <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", $value == '2' ? true : false, ['label' => Yii::t('ezforms', 'Notify when submit'), 'value' => '2']) ?>
                        <!--              </div>
                                    <div class="col-md-12" style="margin-top:10px;">-->
                        <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", $value == '1' ? true : false, ['label' => Yii::t('ezforms', 'Notify when save darft'), 'value' => '1']) ?>
                        <!--            </div>
                                    <div class="col-md-12" style="margin-top:10px;">-->
                        <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", $value == '3' ? true : false, ['label' => Yii::t('ezforms', 'Notify when delete'), 'value' => '3']) ?>
                        <!--</div>-->
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12" style="margin-top:10px;">
                            <?= Html::label(Yii::t('ezform', 'Notification message'), '', ['class' => 'control-label']) ?>
                            <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'notify_savedarft-' . $id]) ?>
                            <?= Html::textInput("options[config_advance][notify][]", $configAdvance['notify'][$key], ['class' => 'form-control', 'id' => 'notify_savedarft-' . $id]) ?>

                        </div>
                        <div class="col-md-12" style="margin-top:10px;">
                            <?= Html::label(Yii::t('ezform', 'Topic'), 'options[title]', ['class' => 'control-label']) ?>
                            <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'topic-' . $id]) ?>
                            <?=
                            \vova07\imperavi\Widget::widget([
                                'id' => 'topic-' . $id,
                                'class' => 'textarea',
                                'name' => 'options[config_advance][detail][]',
                                'value' => $configAdvance['detail'][$key],
                                'settings' => [
                                    'minHeight' => 30,
                                    'imageManagerJson' => '../../ezforms2/text-editor/images-get',
                                    'fileManagerJson' => '../../ezforms2/text-editor/files-get',
                                    'imageUpload' => '../../ezforms2/text-editor/image-upload',
                                    'fileUpload' => '../../ezforms2/text-editor/file-upload',
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
                                    'paragraphize' => false,
                                    'replaceDivs' => false,
                                ],
                            ])
                            ?>
                            <?php // editor('options[options][topic]', isset($options['options']['topic']) ? $options['options']['topic'] : '', ['class' => 'form-control', 'rows' => 7]) ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="form-group row notify"> 
                <!--<hr/>-->
                <div class="modal-header divHeadConfigAssign" style="background-color:#b9b5b8; margin-bottom:10px;">
                    <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Assign') ?></h4>
                </div>
                <div class="divConfigAssign">
                    <div class="col-md-6" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Assign Name'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Select2::widget([
                            'id' => 'name-fix-' . $id,
                            'name' => 'options[config_advance][name_fix][]',
                            'data' => ArrayHelper::map($items_user, 'user_id', 'name'),
                            'value' => isset($configAdvance['name_fix'][$key]) ? $configAdvance['name_fix'][$key] : NULL,
                            'options' => [
                                'placeholder' => 'Select user ...',
                                'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6 sdbox-col"  style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Assign Role'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Select2::widget([
                            'id' => 'role-fix-' . $id,
                            'name' => 'options[config_advance][role_fix][]',
                            'data' => ArrayHelper::map($items_role, 'role_name', 'role_detail'),
                            'value' => isset($configAdvance['role_fix'][$key]) ? $configAdvance['name_fix'][$key] : NULL,
                            'options' => [
                                'placeholder' => 'Select role ...',
                                'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>



                    <div class="col-md-12 notify" style="margin-top:10px;">
                        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_system][]", isset($configAdvance['send_system'][$key]) ? $configAdvance['send_system'][$key] : true, ['label' => Yii::t('ezforms', 'nCRC Notification Center'), 'id' => 'send-system']) ?>
                        <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                                     ?> **</code>-->
                    </div>
                    <div class="col-md-12 notify" style="margin-top:10px;">
                        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_email][]", isset($configAdvance['send_email'][$key]) ? $configAdvance['send_email'][$key] : NULL, ['label' => Yii::t('ezforms', 'E-Mail (Free)'), 'id' => 'send_mail']) ?>
                        <?php // echo Html::textInput("options[options][val_email]", isset($options['options']['val_email']) ? $options['options']['val_email'] : '', ['class' => 'form-control', 'placeholder' => 'Email to..', 'style' => 'display:none', 'id' => 'input-email'])   ?>
                    </div>
                    <div class="col-md-12 notify" style="margin-top:10px;">
                        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_line][]", isset($configAdvance['send_line'][$key]) ? $configAdvance['send_line'][$key] : NULL, ['label' => Yii::t('ezforms', 'Line (Free)'), 'id' => 'send-line']) ?>
                        <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                                    ?> **</code>-->
                    </div>

                    <!--</div>
                    
                    <div class="form-group row notify">
                        <hr/>
                        <div class="modal-header" style="background-color:#b9b5b8; margin-bottom:10px;">
                            <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Format') ?></h4>
                        </div>-->
                    <div class="col-md-4" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Format'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Html::dropDownList("options[config_advance][type_url][]", isset($configAdvance['type_url'][$key]) ? $configAdvance['type_url'][$key] : NULL, ['1' => 'Redirect', '2' => 'Open Form', '3' => 'Not Action'], [
                            'class' => 'form-control',
                            'id' => 'type-' . $id
                        ])
                        ?>
                    </div>
                    <div class="col-md-8" style="margin-top:10px;">
                        <div id="divUrl-<?= $id ?>" style="display: none">
                            <?= Html::label(Yii::t('ezform', 'Url'), 'options[title]', ['class' => 'control-label']) ?>
                            <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'input-url-' . $id]) ?>
                            <?=
                            Html::textarea("options[config_advance][url][]", isset($configAdvance['url'][$key]) ? $configAdvance['url'][$key] : NULL, [
                                'class' => 'form-control',
                                'id' => 'input-url-' . $id
                            ])
                            ?>
                        </div>
                        <div id="divReadonly-<?= $id ?>" style="margin-top:25px; display: none">
                            <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][readonly][]", isset($configAdvance['readonly'][$key]) ? $configAdvance['readonly'][$key] : NULL, ['label' => 'Read only mode']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <!--<hr/>-->
                <div class="modal-header divHeadConfigField-<?= $id ?>" style="background-color:#b9b5b8; margin-bottom:10px;">
                    <span class="pull-right btn btn-info btn-sm" title=""><i class="glyphicon glyphicon-resize-full divHeadConfigFields-<?= $id ?>"></i> More</span>
                    <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Field') ?></h4> 

                </div>

                <div class="divConfigField-<?= $id ?>">
                    <div class="col-md-12 notify-tmf" style="margin-top:10px;">
                        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][mandatory][]", isset($configAdvance['mandatory'][$key]) ? $configAdvance['mandatory'][$key] : NULL, ['label' => 'Mandatory']) ?>
                    </div>

                    <div class="col-md-12 notify" style="margin-top:10px;">
                        <?php
//            echo \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[options][action]', isset($options['options']['action']) ? $options['options']['action'] : NULL, ['label' => 'Have action']);
                        ?>
                    </div>

                    <div class="col-md-6 " style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Field Assign Name'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Select2::widget([
                            'id' => 'alert-name-' . $id,
                            'name' => 'options[config_advance][alert_name][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                            'value' => isset($configAdvance['alert_name'][$key]) ? $configAdvance['alert_name'][$key] : NULL,
                            'initValueText' => (new \yii\db\Query())
                                    ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                    ->from('ezform_fields')
                                    ->where([
                                        'ezf_id' => $ezf_id,
                                        'ezf_version' => $ezf_version,
                                        'ezf_field_type' => '906',
                                        'ezf_field_name' => isset($configAdvance['alert_name'][$key]) ? $configAdvance['alert_name'][$key] : ''
                                    ])
                                    ->andWhere("table_field_type not in('none','field')")
                                    ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                    ->scalar(),
                            'options' => [
                                'placeholder' => 'Select field ...',
//            'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'ajax' => [
                                    'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([906]) . '&ezf_version=' . $queryTool['ezf_version'],
                                    'dataType' => 'json', //รูปแบบการอ่านคือ json
                                    'data' => new JsExpression('function(params) { return {q:params.term};}')
                                ],
                                'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                                'templateResult' => new JsExpression('function(results){ return results.text;}'),
                                'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Field Assign Role'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Select2::widget([
                            'id' => 'alert-role-' . $id,
                            'name' => 'options[config_advance][alert_role][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                            'value' => isset($configAdvance['alert_role'][$key]) ? $configAdvance['alert_role'][$key] : NULL,
                            'initValueText' => (new \yii\db\Query())
                                    ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                    ->from('ezform_fields')
                                    ->where([
                                        'ezf_id' => $ezf_id,
                                        'ezf_version' => $ezf_version,
                                        'ezf_field_type' => '907',
                                        'ezf_field_name' => isset($configAdvance['alert_role'][$key]) ? $configAdvance['alert_role'][$key] : ''
                                    ])
                                    ->andWhere("table_field_type not in('none','field')")
                                    ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                    ->scalar(),
                            'options' => [
                                'placeholder' => 'Select field ...',
//                    'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'ajax' => [
                                    'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([907]) . '&ezf_version=' . $queryTool['ezf_version'],
                                    'dataType' => 'json', //รูปแบบการอ่านคือ json
                                    'data' => new JsExpression('function(params) { return {q:params.term};}')
                                ],
                                'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                                'templateResult' => new JsExpression('function(results){ return results.text;}'),
                                'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                            ],
                        ]);
                        ?>
                    </div>

                    <div class="col-md-6 notify-tmf" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Field Action'), 'options[title]', ['class' => 'control-label']) ?>
                        <?=
                        Select2::widget([
                            'id' => 'action-field-' . $id,
                            'name' => 'options[config_advance][field_action][]',
                            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                            'value' => isset($configAdvance['field_action'][$key]) ? $configAdvance['field_action'][$key] : NULL,
                            'options' => [
                                'placeholder' => 'Select field ...',
//            'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="clearfix"></div>

                </div>

            </div>



            <div class="form-group row notify-tmf divConfigField-<?= $id ?>"> 
                <!--<hr/>-->
                <hr>
                <div class="col-md-6 notify" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Delay Date'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'delay-field-' . $id,
                        'name' => 'options[config_advance][field_delay][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => isset($configAdvance['field_delay'][$key]) ? $configAdvance['field_delay'][$key] : NULL,
                        'initValueText' => (new \yii\db\Query())
                                ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                ->from('ezform_fields')
                                ->where([
                                    'ezf_id' => $ezf_id,
                                    'ezf_version' => $ezf_version,
                                    'ezf_field_name' => isset($configAdvance['field_delay'][$key]) ? $configAdvance['field_delay'][$key] : ''
                                ])
                                ->andWhere("table_field_type not in('none','field') AND ezf_field_type in(63,64)")
                                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                ->scalar(),
                        'options' => [
                            'placeholder' => 'Select field ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Due Date Review'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'date_review-' . $id,
                        'name' => 'options[config_advance][due_date_review][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => isset($configAdvance['due_date_review'][$key]) ? $configAdvance['due_date_review'][$key] : NULL,
                        'initValueText' => (new \yii\db\Query())
                                ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                ->from('ezform_fields')
                                ->where([
                                    'ezf_id' => $ezf_id,
                                    'ezf_version' => $ezf_version,
//                        'ezf_field_type' => '906',
                                    'ezf_field_name' => isset($configAdvance['due_date_review'][$key]) ? $configAdvance['due_date_review'][$key] : ''
                                ])
                                ->andWhere("table_field_type not in('none','field') AND ezf_field_type in(63,64)")
                                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                ->scalar(),
                        'options' => [
                            'placeholder' => 'Select field ...',
//                    'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6 " style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Due Date Approve'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'date_approve-' . $id,
                        'name' => 'options[config_advance][due_date_approve][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => isset($configAdvance['due_date_approve'][$key]) ? $configAdvance['due_date_approve'][$key] : NULL,
                        'initValueText' => (new \yii\db\Query())
                                ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                ->from('ezform_fields')
                                ->where([
                                    'ezf_id' => $ezf_id,
                                    'ezf_version' => $ezf_version,
//                        'ezf_field_type' => '906',
                                    'ezf_field_name' => isset($configAdvance['due_date_approve'][$key]) ? $configAdvance['due_date_approve'][$key] : ''
                                ])
                                ->andWhere("table_field_type not in('none','field') AND ezf_field_type in(63,64)")
                                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                ->scalar(),
                        'options' => [
                            'placeholder' => 'Select field ...',
//                    'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <!--    <div class="col-md-4 sdbox-col" style="margin-top:10px; ">
                <?php
// echo Html::label(Yii::t('ezform', 'Field Due Date Acknowledge'), 'options[title]', ['class' => 'control-label']);
//        echo Select2::widget([
//            'id' => 'date_acknowledge',
//            'name' => 'options[options][due_date_acknowledge]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
//            'value' => isset($options['options']['due_date_acknowledge']) ? $options['options']['due_date_acknowledge'] : NULL,
//            'options' => [
//                'placeholder' => 'Select field ...',
////                    'multiple' => true
//            ],
//            'pluginOptions' => [
//                'allowClear' => true
//            ],
//        ]);
                ?>
                    </div>-->

                <!--</div>-->

                <!--<div class="form-group row divConfigField">--> 
                <!--<hr/>-->

                <!--    <div class="col-md-12">-->

                <div class="col-md-6 notify-tmf" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Effective Date'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'effective-date-' . $id,
                        'name' => 'options[config_advance][effective_date][]',
//                'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => isset($configAdvance['effective_date'][$key]) ? $configAdvance['effective_date'][$key] : NULL,
                        'initValueText' => (new \yii\db\Query())
                                ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                ->from('ezform_fields')
                                ->where([
                                    'ezf_id' => $ezf_id,
                                    'ezf_version' => $ezf_version,
//                        'ezf_field_type' => '906',
                                    'ezf_field_name' => isset($configAdvance['effective_date'][$key]) ? $configAdvance['effective_date'][$key] : ''
                                ])
                                ->andWhere("table_field_type not in('none','field') AND ezf_field_type in(63,64)")
                                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                ->scalar(),
                        'options' => [
                            'placeholder' => 'Select field ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6 notify-tmf"  style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Due Date'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'due-date-' . $id,
                        'name' => 'options[config_advance][due_date][]',
//                'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => isset($configAdvance['due_date'][$key]) ? $configAdvance['due_date'][$key] : NULL,
                        'initValueText' => (new \yii\db\Query())
                                ->select(['concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`'])
                                ->from('ezform_fields')
                                ->where([
                                    'ezf_id' => $ezf_id,
                                    'ezf_version' => $ezf_version,
//                        'ezf_field_type' => '906',
                                    'ezf_field_name' => isset($configAdvance['due_date'][$key]) ? $configAdvance['due_date'][$key] : ''
                                ])
                                ->andWhere("table_field_type not in('none','field') AND ezf_field_type in(63,64)")
                                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                                ->scalar(),
                        'options' => [
                            'placeholder' => 'Select field ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <div class="clearfix"></div>



                <!--</div>-->

            </div>

            <?php
            richardfan\widget\JSRegister::begin();
            ?>
            <script>


                $('.divConfigField-<?= $id ?>').hide();

                $('.divHeadConfigField-<?= $id ?>').css('cursor', 'pointer');

                $('.divHeadConfigField-<?= $id ?>').click(function () {
                    if ($('.divHeadConfigFields-<?= $id ?>').hasClass('glyphicon-resize-full')) {
                        $('.divHeadConfigFields-<?= $id ?>').removeClass('glyphicon-resize-full');
                        $('.divHeadConfigFields-<?= $id ?>').addClass('glyphicon-resize-small');
                    } else {
                        $('.divHeadConfigFields-<?= $id ?>').removeClass('glyphicon-resize-small');
                        $('.divHeadConfigFields-<?= $id ?>').addClass('glyphicon-resize-full');
                    }
                    $('.divConfigField-<?= $id ?>').toggle('slow');
                });


                $('#type-<?= $id ?>').on('change', function () {
                    if ($('#type-<?= $id ?>').val() == '1') {
                        $('#divUrl-<?= $id ?>').show();
                        $('#divReadonly-<?= $id ?>').hide('slow');
                    } else if ($('#type-<?= $id ?>').val() == '2') {
                        $('#divUrl-<?= $id ?>').hide();
                        $('#divReadonly-<?= $id ?>').show('slow');
                        $('#input-url-<?= $id ?>').val('');
                    } else {
                        $('#divUrl-<?= $id ?>').hide('slow');
                        $('#divReadonly-<?= $id ?>').hide('slow');
                        $('#input-url-<?= $id ?>').val('');
                    }
                });

                if ($('#type-<?= $id ?>').val() == '1') {
                    $('#divUrl-<?= $id ?>').show();
                    $('#divReadonly-<?= $id ?>').hide('slow');
                } else if ($('#type-<?= $id ?>').val() == '2') {
                    $('#divUrl-<?= $id ?>').hide();
                    $('#divReadonly-<?= $id ?>').show('slow');
                    $('#input-url-<?= $id ?>').val('');
                } else {
                    $('#divUrl-<?= $id ?>').hide('slow');
                    $('#divReadonly-<?= $id ?>').hide('slow');
                    $('#input-url-<?= $id ?>').val('');
                }

                $('.btn-remove-div').click(function () {
                    $(this).parents('.divMainAdvance-<?= $id ?>').remove();
                });
            </script>
            <?php
            richardfan\widget\JSRegister::end();
        }
        ?>
    </div>
<?php } else {
    ?>
    <div class="divMainAdvance-<?= $id ?>">
        <div class="form-group row notify"> 
             <?= Html::button('&times;', ['class' => 'close btn-remove-div']); ?>
            <div class="clearfix"></div>
            <hr/>
            <div class="modal-header divHeadConfigMessage" style="background-color:#b9b5b8 ">
                <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Message') ?></h4>
            </div>

            <div class="divConfigMessage">
                <div class="col-md-3" style="margin-top:10px;">
                    <!--<div class="col-md-12 " style="margin-top:10px;">-->
                    <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", '', ['label' => Yii::t('ezforms', 'Notify when submit'), 'value' => '2']) ?>
                    <!--              </div>
                                <div class="col-md-12" style="margin-top:10px;">-->
                    <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", '', ['label' => Yii::t('ezforms', 'Notify when save darft'), 'value' => '1']) ?>
                    <!--            </div>
                                <div class="col-md-12" style="margin-top:10px;">-->
                    <?= \backend\modules\ezforms2\classes\EzformWidget::radio("options[config_advance][action_choice][]", '', ['label' => Yii::t('ezforms', 'Notify when delete'), 'value' => '3']) ?>
                    <!--</div>-->
                </div>
                <div class="col-md-9">
                    <div class="col-md-12" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Notification message'), '', ['class' => 'control-label']) ?>
                        <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'notify_savedarft-' . $id]) ?>
                        <?= Html::textInput("options[config_advance][notify][]", '', ['class' => 'form-control', 'id' => 'notify_savedarft-' . $id]) ?>

                    </div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Topic'), 'options[title]', ['class' => 'control-label']) ?>
                        <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'topic-' . $id]) ?>
                        <?=
                        \vova07\imperavi\Widget::widget([
                            'id' => 'topic-' . $id,
                            'class' => 'textarea',
                            'name' => 'options[config_advance][detail][]',
                            'value' => '',
                            'settings' => [
                                'minHeight' => 30,
                                'imageManagerJson' => '../../ezforms2/text-editor/images-get',
                                'fileManagerJson' => '../../ezforms2/text-editor/files-get',
                                'imageUpload' => '../../ezforms2/text-editor/image-upload',
                                'fileUpload' => '../../ezforms2/text-editor/file-upload',
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
                                'paragraphize' => false,
                                'replaceDivs' => false,
                            ],
                        ])
                        ?>
                        <?php // editor('options[options][topic]', isset($options['options']['topic']) ? $options['options']['topic'] : '', ['class' => 'form-control', 'rows' => 7]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="form-group row notify"> 
            <!--<hr/>-->
            <div class="modal-header divHeadConfigAssign" style="background-color:#b9b5b8; margin-bottom:10px;">
                <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Assign') ?></h4>
            </div>
            <div class="divConfigAssign">
                <div class="col-md-6" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Assign Name'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'name-fix-' . $id,
                        'name' => 'options[config_advance][name_fix][]',
                        'data' => ArrayHelper::map($items_user, 'user_id', 'name'),
                        'value' => '',
                        'options' => [
                            'placeholder' => 'Select user ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6 sdbox-col"  style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Assign Role'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'role-fix-' . $id,
                        'name' => 'options[config_advance][role_fix][]',
                        'data' => ArrayHelper::map($items_role, 'role_name', 'role_detail'),
                        'value' => '',
                        'options' => [
                            'placeholder' => 'Select role ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>



                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_system][]", '', ['label' => Yii::t('ezforms', 'nCRC Notification Center'), 'id' => 'send-system']) ?>
                    <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                                    ?> **</code>-->
                </div>
                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_email][]", '', ['label' => Yii::t('ezforms', 'E-Mail (Free)'), 'id' => 'send_mail']) ?>
                    <?php // echo Html::textInput("options[options][val_email]", isset($options['options']['val_email']) ? $options['options']['val_email'] : '', ['class' => 'form-control', 'placeholder' => 'Email to..', 'style' => 'display:none', 'id' => 'input-email'])   ?>
                </div>
                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][send_line][]", '', ['label' => Yii::t('ezforms', 'Line (Free)'), 'id' => 'send-line']) ?>
                    <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                                   ?> **</code>-->
                </div>

                <!--</div>
                
                <div class="form-group row notify">
                    <hr/>
                    <div class="modal-header" style="background-color:#b9b5b8; margin-bottom:10px;">
                        <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Format') ?></h4>
                    </div>-->
                <div class="col-md-4" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Format'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Html::dropDownList("options[config_advance][type_url][]", '', ['1' => 'Redirect', '2' => 'Open Form', '3' => 'Not Action'], [
                        'class' => 'form-control',
                        'id' => 'type-' . $id
                    ])
                    ?>
                </div>
                <div class="col-md-8" style="margin-top:10px;">
                    <div id="divUrl-<?= $id ?>" style="display: none">
                        <?= Html::label(Yii::t('ezform', 'Url'), 'options[title]', ['class' => 'control-label']) ?>
                        <?= Html::button(Yii::t('ezform', 'Add constant'), ['class' => 'btn btn-success btn-xs btn-add-constant', 'data-input' => 'input-url-' . $id]) ?>
                        <?=
                        Html::textarea("options[config_advance][url][]", '', [
                            'class' => 'form-control',
                            'id' => 'input-url-' . $id
                        ])
                        ?>
                    </div>
                    <div id="divReadonly-<?= $id ?>" style="margin-top:25px; display: none">
                        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][readonly][]", '', ['label' => 'Read only mode']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <!--<hr/>-->
            <div class="modal-header divHeadConfigField-<?= $id ?>" style="background-color:#b9b5b8; margin-bottom:10px;">
                <span class="pull-right btn btn-info btn-sm" title=""><i class="glyphicon glyphicon-resize-full divHeadConfigFields-<?= $id ?>"></i> More</span>
                <h4 class="modal-title"><?= Yii::t('ezforms', 'Config Field') ?></h4> 

            </div>

            <div class="divConfigField-<?= $id ?>">
                <div class="col-md-12 notify-tmf" style="margin-top:10px;">
                    <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox("options[config_advance][mandatory][]", '', ['label' => 'Mandatory']) ?>
                </div>

                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?php
//            echo \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[options][action]', isset($options['options']['action']) ? $options['options']['action'] : NULL, ['label' => 'Have action']);
                    ?>
                </div>

                <div class="col-md-6 " style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Assign Name'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'alert-name-' . $id,
                        'name' => 'options[config_advance][alert_name][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => '',
                        'initValueText' => '',
                        'options' => [
                            'placeholder' => 'Select field ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([906]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Assign Role'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'alert-role-' . $id,
                        'name' => 'options[config_advance][alert_role][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => '',
                        'initValueText' => '',
                        'options' => [
                            'placeholder' => 'Select field ...',
//                    'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([907]) . '&ezf_version=' . $queryTool['ezf_version'],
                                'dataType' => 'json', //รูปแบบการอ่านคือ json
                                'data' => new JsExpression('function(params) { return {q:params.term};}')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                            'templateResult' => new JsExpression('function(results){ return results.text;}'),
                            'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                        ],
                    ]);
                    ?>
                </div>

                <div class="col-md-6 notify-tmf" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Field Action'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'action-field-' . $id,
                        'name' => 'options[config_advance][field_action][]',
                        'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                        'value' => '',
                        'options' => [
                            'placeholder' => 'Select field ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
                <div class="clearfix"></div>

            </div>

        </div>



        <div class="form-group row notify-tmf divConfigField-<?= $id ?>"> 
            <!--<hr/>-->
            <hr>
            <div class="col-md-6 notify" style="margin-top:10px;">
                <?= Html::label(Yii::t('ezform', 'Field Delay Date'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Select2::widget([
                    'id' => 'delay-field-' . $id,
                    'name' => 'options[config_advance][field_delay][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                    'value' => '',
                    'initValueText' => '',
                    'options' => [
                        'placeholder' => 'Select field ...',
//            'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                            'dataType' => 'json', //รูปแบบการอ่านคือ json
                            'data' => new JsExpression('function(params) { return {q:params.term};}')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                        'templateResult' => new JsExpression('function(results){ return results.text;}'),
                        'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6" style="margin-top:10px;">
                <?= Html::label(Yii::t('ezform', 'Field Due Date Review'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Select2::widget([
                    'id' => 'date_review-' . $id,
                    'name' => 'options[config_advance][due_date_review][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                    'value' => '',
                    'initValueText' => '',
                    'options' => [
                        'placeholder' => 'Select field ...',
//                    'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                            'dataType' => 'json', //รูปแบบการอ่านคือ json
                            'data' => new JsExpression('function(params) { return {q:params.term};}')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                        'templateResult' => new JsExpression('function(results){ return results.text;}'),
                        'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6 " style="margin-top:10px;">
                <?= Html::label(Yii::t('ezform', 'Field Due Date Approve'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Select2::widget([
                    'id' => 'date_approve-' . $id,
                    'name' => 'options[config_advance][due_date_approve][]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                    'value' => '',
                    'initValueText' => '',
                    'options' => [
                        'placeholder' => 'Select field ...',
//                    'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                            'dataType' => 'json', //รูปแบบการอ่านคือ json
                            'data' => new JsExpression('function(params) { return {q:params.term};}')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                        'templateResult' => new JsExpression('function(results){ return results.text;}'),
                        'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                    ],
                ]);
                ?>
            </div>
            <!--    <div class="col-md-4 sdbox-col" style="margin-top:10px; ">
            <?php
// echo Html::label(Yii::t('ezform', 'Field Due Date Acknowledge'), 'options[title]', ['class' => 'control-label']);
//        echo Select2::widget([
//            'id' => 'date_acknowledge',
//            'name' => 'options[options][due_date_acknowledge]',
//            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
//            'value' => isset($options['options']['due_date_acknowledge']) ? $options['options']['due_date_acknowledge'] : NULL,
//            'options' => [
//                'placeholder' => 'Select field ...',
////                    'multiple' => true
//            ],
//            'pluginOptions' => [
//                'allowClear' => true
//            ],
//        ]);
            ?>
                </div>-->

            <!--</div>-->

            <!--<div class="form-group row divConfigField">--> 
            <!--<hr/>-->

            <!--    <div class="col-md-12">-->

            <div class="col-md-6 notify-tmf" style="margin-top:10px;">
                <?= Html::label(Yii::t('ezform', 'Field Effective Date'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Select2::widget([
                    'id' => 'effective-date-' . $id,
                    'name' => 'options[config_advance][effective_date][]',
//                'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                    'value' => '',
                    'initValueText' => '',
                    'options' => [
                        'placeholder' => 'Select field ...',
//            'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                            'dataType' => 'json', //รูปแบบการอ่านคือ json
                            'data' => new JsExpression('function(params) { return {q:params.term};}')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                        'templateResult' => new JsExpression('function(results){ return results.text;}'),
                        'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6 notify-tmf"  style="margin-top:10px;">
                <?= Html::label(Yii::t('ezform', 'Field Due Date'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Select2::widget([
                    'id' => 'due-date-' . $id,
                    'name' => 'options[config_advance][due_date][]',
//                'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
                    'value' => '',
                    'initValueText' => '',
                    'options' => [
                        'placeholder' => 'Select field ...',
//            'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => '/ezforms2/notify-input/get-field?ezf_id=' . $ezf_id . '&ezf_field_id=' . \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64]) . '&ezf_version=' . $queryTool['ezf_version'],
                            'dataType' => 'json', //รูปแบบการอ่านคือ json
                            'data' => new JsExpression('function(params) { return {q:params.term};}')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup) { return markup;}'),
                        'templateResult' => new JsExpression('function(results){ return results.text;}'),
                        'templateSelection' => new JsExpression('function(results) {return results.text;}'),
                    ],
                ]);
                ?>
            </div>
            <div class="clearfix"></div>



            <!--</div>-->

        </div>
        <?php
        richardfan\widget\JSRegister::begin();
        ?>
        <script>
            $('.divConfigField-<?= $id ?>').hide();

            $('.divHeadConfigField-<?= $id ?>').css('cursor', 'pointer');

            $('.divHeadConfigField-<?= $id ?>').click(function () {
                if ($('.divHeadConfigFields-<?= $id ?>').hasClass('glyphicon-resize-full')) {
                    $('.divHeadConfigFields-<?= $id ?>').removeClass('glyphicon-resize-full');
                    $('.divHeadConfigFields-<?= $id ?>').addClass('glyphicon-resize-small');
                } else {
                    $('.divHeadConfigFields-<?= $id ?>').removeClass('glyphicon-resize-small');
                    $('.divHeadConfigFields-<?= $id ?>').addClass('glyphicon-resize-full');
                }
                $('.divConfigField-<?= $id ?>').toggle('slow');
            });

            $('#type-<?= $id ?>').on('change', function () {
                if ($('#type-<?= $id ?>').val() == '1') {
                    $('#divUrl-<?= $id ?>').show();
                    $('#divReadonly-<?= $id ?>').hide('slow');
                } else if ($('#type-<?= $id ?>').val() == '2') {
                    $('#divUrl-<?= $id ?>').hide('slow');
                    $('#divReadonly-<?= $id ?>').show('slow');
                    $('#input-url-<?= $id ?>').val('');
                } else {
                    $('#divUrl-<?= $id ?>').hide('slow');
                    $('#divReadonly-<?= $id ?>').hide('slow');
                    $('#input-url-<?= $id ?>').val('');
                }
            });

            if ($('#type-<?= $id ?>').val() == '1') {
                $('#divUrl-<?= $id ?>').show();
                $('#divReadonly-<?= $id ?>').hide('slow');
            } else if ($('#type-<?= $id ?>').val() == '2') {
                $('#divUrl-<?= $id ?>').hide();
                $('#divReadonly-<?= $id ?>').show('slow');
                $('#input-url-<?= $id ?>').val('');
            } else {
                $('#divUrl-<?= $id ?>').hide('slow');
                $('#divReadonly-<?= $id ?>').hide('slow');
                $('#input-url-<?= $id ?>').val('');
            }

            $('.btn-remove-div').click(function () {
                $(this).parents('.divMainAdvance-<?= $id ?>').remove();
            });

        </script>
        <?php
        richardfan\widget\JSRegister::end();
        ?>
    </div>

    <?php
}
$idModal = 'modal-constant';
$submodal = '<div id="' . $idModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>';

richardfan\widget\JSRegister::begin();
?>
<script>
    var hasMyModal = $('body').has('#<?= $idModal ?>').length;

    if (!hasMyModal) {
        $('#ezf-modal-box').append('<?= $submodal ?>');
    }

    $('#<?= $idModal ?>').on('hidden.bs.modal', function (e) {
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });
    $(".btn-add-constant").on('click', function () {
        var data_input = $(this).attr('data-input');
        $('#<?= $idModal ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $idModal ?>').modal('show')
                .find('.modal-content')
                .load('/ezforms2/notify-input/constant?ezf_id=<?= $ezf_id ?>&version=<?= $ezf_version ?>&modal=<?= $idModal ?>&id_input=' + data_input);

//        var $txt = $("#"+data_input);
//        var caretPos = $txt[0].selectionStart;
//        var textAreaTxt = $txt.val();
//        var txtToAdd = "stuff";
//        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
    });

</script>
<?php
richardfan\widget\JSRegister::end();

