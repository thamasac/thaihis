<?php

use kartik\select2\Select2;
use yii\web\JsExpression;
?>
<div class="row"><div class="col-md-12"><label class="control-label" for="tbltree-name"><?= Yii::t('ezform', 'Explanation')?></label> </div></div>
<div class="row">
    <div class="col-md-2 col-md-offset-1"><span class="text-warning kv-node-icon kv-icon-parent fa fa-folder kv-node-closed"> = <?= Yii::t('ezform', 'Group (Close)')?></span> </div>
    <div class="col-md-2"><span class="text-warning kv-node-icon kv-icon-parent fa fa-folder-open kv-node-opened"> = <?= Yii::t('ezform', 'Group (Open)')?></span> </div>
    <div class="col-md-2"><span class="text-info kv-node-icon kv-icon-parent fa fa-file kv-node-file"> = <?= Yii::t('ezform', 'Sub Group')?></span> </div>
    <div class="col-md-2"><span class="text-info kv-node-icon kv-icon-parent fa fa-institution kv-node-closed"> = <?= Yii::t('ezform', 'Project')?></span></div>
    <div class="col-md-3"><span class="text-info kv-node-icon kv-icon-parent fa fa-user kv-node-user"> = <?= Yii::t('ezform', 'User')?></span> </div>
</div>

<?php

if (Yii::$app->user->can('administrator')) {
    echo '<div class="modal-header"><h4>'.Yii::t('ezform', 'for administrator').'</h4></div>';
    echo '<div class="modal-body">';
    echo $form->field($node, 'readonly')->checkbox(['label' => Yii::t('ezform', 'Read Only')]);
    //echo $form->field($node, 'collapsed')->checkbox(['label' => 'หุบกิจกรรมไว้']);
    //
    //echo $form->field($node, 'readonly', 'ประเภทกิจกรรม')->widget(Select2::classname(), [
    //    'data' => [1 => "กิจกรรมของระบบ (เห็นทุกคน, แก้ไขไม่ได้)", 0 => "กิจกรรมส่วนตัว"],
    //    'options' => ['placeholder' => 'กรุณาเลือกชนิด'],
    //    'pluginOptions' => [
    //        'allowClear' => false
    //    ],
    //]);
    
    $datavalue = \common\modules\user\models\Profile::find($node->userid)->one();
    echo '<b>Create By:</b> ' . $datavalue->firstname. ' ' . $datavalue->lastname;
    
//    echo $form->field($node, 'userid')->widget(Select2::className(), [
//            'initValueText' => $datavalue->firstname. ' ' . $datavalue->lastname,
//            'options' => ['placeholder' => Yii::t('ezform', 'Please select')],
//            'pluginOptions' => [
//                'allowClear' => false,
//                'ajax' => [
//                    'url' => yii\helpers\Url::to(['/ezforms2/ezform/get-user']),
//                    'dataType' => 'json',
//                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
//                ],
//                //'tags' => true,
//                //'tokenSeparators' => [',', ' '],
//                //'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
//                //'templateResult' => new JsExpression('function(result) { return result.text; }'),
//                //'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
//            ],
//        ]);
//    echo $form->field($node, 'userid')->dropdownlist(\yii\helpers\ArrayHelper::map(\common\modules\user\models\Profile::find()->all(), 'user_id', 'firstname'));
//    echo $form->field($node, 'userid')->widget(Select2::classname(), [
//        'value'=>$node->userid,
//        'data' => \yii\helpers\ArrayHelper::map(\common\modules\user\models\Profile::find()->all(), 'user_id', 'firstname'),
//        'options' => ['placeholder' => Yii::t('ezform', 'Please select')],
//        'pluginOptions' => [
//            'allowClear' => false
//        ],
//    ]);
//    $node->ezf_id = explode(',', $node->ezf_id);
//    echo $form->field($node, 'ezf_id')->widget(Select2::classname(), [
//        'data' => \yii\helpers\ArrayHelper::map(backend\models\Ezform::find()->where('ezf_id>100000 and status=1')->all(),'ezf_id','ezf_name'),
//        'options' => ['placeholder' => 'กรุณาเลือกฟอร์มบันทึกข้อมูล'],
//        'pluginOptions' => [
//            'allowClear' => true,
//            'multiple'  => true,
//        ],
//    ]);
    echo '</div>';
}
?>