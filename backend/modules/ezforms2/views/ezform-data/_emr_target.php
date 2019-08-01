<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}
//Yii::$app->session['ezform'] = $modelEzf->attributes;
if(!$modelFields){
    $modelFields = EzfQuery::getFieldAllVersion($modelEzf->ezf_id);
    $modelFields = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);
}

$modelForm = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
$modelForm[$targetField['ezf_field_name']] = $target;

?>

<?php

$formName = 'target-emr-' . $model->formName();
$form = yii\bootstrap\ActiveForm::begin([
            'id' => $formName,
            'action' => Yii::$app->request->url,
            'method' => 'get',
            'layout' => 'inline',
        ]);
?>
<?php // echo Html::label(Yii::t('ezform', 'Target')) ?>
<?php

$options = SDUtility::string2ArrayJs($targetField['ezf_field_options']);
unset($options['specific']);

if (isset($options['options']['data-func-set']) && !empty($options['options']['data-func-set'])) {
    $pathStr = [
        '{model}' => "\$modelForm",
        '{modelFields}' => "\$targetField",
    ];

    $funcSet = strtr($options['options']['data-func-set'], $pathStr);

    try {
        $initial = eval("return $funcSet;");
    } catch (\yii\base\Exception $e) {
        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        $initial = FALSE;
    }

    if ($initial) {
        if (isset($options['options']['data-name-in'])) {
            $data_in = $options['options']['data-name-in'];
            $data_set = self::addProperty($data_in, $options['options']['data-name-set'], $initial);
            $options = ArrayHelper::merge($options, $data_set);
        } else {
            $options[$options['options']['data-name-set']] = $initial;
        }
    }
}
$options['id'] = 'SD_'.$modelEzf->ezf_id;
$options['model'] = $model;
$options['attribute'] = 'target_id';
$options['pluginOptions']['allowClear'] = true;
$options['options']['placeholder'] = Yii::t('ezform', 'All Target');

echo \appxq\sdii\widgets\SDTarget::widget($options);
//\appxq\sdii\utils\VarDumper::dump($options);
?>

<?php yii\bootstrap\ActiveForm::end(); ?>

<?php

$this->registerJs("
$('form#$formName').on('change', function(e) {
    $(this).submit();   
});

$('form#$formName').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});
");
?>