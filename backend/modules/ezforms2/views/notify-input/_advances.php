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
    if ($value['ezf_field_label'] && $value['ezf_field_type'] != 0) {
        $item_field[$key]['ezf_field_label'] = $value['ezf_field_label'] . " (" . $value['ezf_field_name'] . ")";
    } else {
        unset($item_field[$key]);
    }
}
//appxq\sdii\utils\VarDumper::dump($configAdvance);
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
if (!empty($configAdvance)) {

    foreach ($configAdvance['con_field'] as $key => $value) {
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        echo Html::beginTag('div', ['class' => 'divMainAdvance']);

        echo Html::beginTag('div', ['class' => 'col-md-3']);
        echo Html::label(Yii::t('ezform', 'Field'), 'options[title]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'field-con-' . $id,
            'name' => 'options[options][config_advance][con_field][]',
            'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
            'value' => isset($configAdvance['con_field'][$key]) ? $configAdvance['con_field'][$key] : NULL,
            'options' => [
                'placeholder' => 'Select field ...',
//            'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]);

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('notify', 'Condition'), 'options[title]', ['class' => 'control-label']);
//        echo Select2::widget([
//            'id' => 'con-' . $id,
//            'name' => 'options[options][config_advance][con_condition][]',
//            'hideSearch' => true,
//            'data' => ['<' => '<', '>' => '>', '==' => '='],
//            'value' => isset($configAdvance['con_condition'][$key]) ? $configAdvance['con_condition'][$key] : NULL,
//            'options' => [
//                'placeholder' => 'Select field ...',
////            'multiple' => true
//            ],
//            'pluginOptions' => [
//                'allowClear' => true,
//            ],
//        ]);

        echo Html::dropDownList('options[options][config_advance][con_condition][]', isset($configAdvance['con_condition'][$key]) ? $configAdvance['con_condition'][$key] : NULL, ['==' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=','!=' => '!='], ['class' => 'form-control']);

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-4']);
        echo Html::label(Yii::t('ezform', 'Value'), 'options[title]', ['class' => 'control-label']);
        echo Html::textInput('options[options][config_advance][con_value][]', isset($configAdvance['con_value'][$key]) ? $configAdvance['con_value'][$key] : NULL, ['class' => 'form-control']);

        echo Html::endTag('div');


        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('notify', 'Condition'), 'options[title]', ['class' => 'control-label']);
//        echo Select2::widget([
//            'id' => 'con-choice' . $id,
//            'name' => 'options[options][config_advance][con_choice][]',
//            'hideSearch' => true,
//            'data' => ['&&' => 'AND', '||' => 'OR'],
//            'value' => isset($configAdvance['con_choice'][$key]) ? $configAdvance['con_choice'][$key] : NULL,
//            'options' => [
//                'placeholder' => 'None',
////            'multiple' => true
//            ],
//            'pluginOptions' => [
//                'allowClear' => true,
//            ],
//        ]);
        echo Html::dropDownList('options[options][config_advance][con_choice][]', isset($configAdvance['con_choice'][$key]) ? $configAdvance['con_choice'][$key] : NULL, ['' => 'None', '&&' => 'AND', '||' => 'OR'], ['class' => 'form-control']);

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-1']);
        echo Html::button('x', ['class' => 'close btn-remove-divcon']);
        echo Html::endTag('div');

        echo Html::tag('div', '', ['class' => 'clearfix']);
        echo Html::tag('hr');

        echo Html::endTag('div');
    }
} else {
    echo Html::beginTag('div', ['class' => 'divMainAdvance']);

    echo Html::beginTag('div', ['class' => 'col-md-3']);
    echo Html::label(Yii::t('ezform', 'Field'), 'options[title]', ['class' => 'control-label']);
    echo Select2::widget([
        'id' => 'field-con-' . $id,
        'name' => 'options[options][config_advance][con_field][]',
        'data' => ArrayHelper::map($item_field, 'ezf_field_name', 'ezf_field_label'),
        'value' => NULL,
        'options' => [
            'placeholder' => 'Select field ...',
//            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-md-2']);
    echo Html::label(Yii::t('notify', 'Condition'), 'options[title]', ['class' => 'control-label']);
//    echo Select2::widget([
//        'id' => 'con-' . $id,
//        'name' => 'options[options][config_advance][con_condition][]',
//        'hideSearch' => true,
//        'data' => ['<' => '<', '>' => '>', '==' => '='],
//        'value' => NULL,
//        'options' => [
//            'placeholder' => 'Select condition ...',
////            'multiple' => true
//        ],
//        'pluginOptions' => [
//            'allowClear' => true,
//        ],
//    ]);
    echo Html::dropDownList('options[options][config_advance][con_condition][]', '', ['==' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=','!=' => '!='], ['class' => 'form-control']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-md-4']);
    echo Html::label(Yii::t('ezform', 'Value'), 'options[title]', ['class' => 'control-label']);
    echo Html::textInput('options[options][config_advance][con_value][]', '', ['class' => 'form-control']);

    echo Html::endTag('div');


    echo Html::beginTag('div', ['class' => 'col-md-2']);
    echo Html::label(Yii::t('notify', 'Condition'), 'options[title]', ['class' => 'control-label']);
//    echo Select2::widget([
//        'id' => 'con-choice-' . $id,
//        'name' => 'options[options][config_advance][con_choice][]',
//        'hideSearch' => true,
//        'data' => ['AND' => '&&', 'OR' => '||'],
//        'value' => NULL,
//        'options' => [
//            'placeholder' => 'None',
////            'multiple' => true
//        ],
//        'pluginOptions' => [
//            'allowClear' => true,
//        ],
//    ]);
    echo Html::dropDownList('options[options][config_advance][con_choice][]', NULL, ['' => 'None', '&&' => 'AND', '||' => 'OR'], ['class' => 'form-control']);
    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-md-1']);
    echo Html::button('x', ['class' => 'close btn-remove-divcon']);
    echo Html::endTag('div');
    echo Html::tag('div', '', ['class' => 'clearfix']);
    echo Html::tag('hr');
    echo Html::endTag('div');
}
$idModal = 'modal-constant';
$submodal = '<div id="' . $idModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>';

richardfan\widget\JSRegister::begin();
?>
<script>

    $('.btn-remove-divcon').click(function () {
        $(this).parents('.divMainAdvance').remove();
    });

</script>
<?php

richardfan\widget\JSRegister::end();

