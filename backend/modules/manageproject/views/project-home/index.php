<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use \appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$ezf_content = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->params['web_content_form']);
$ezf_id = $ezf_content[1];
$modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);

$getProject = isset(Yii::$app->params['model_dynamic']) ? Yii::$app->params['model_dynamic'] : '';

if (!empty($getProject)) {
    $myproject = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProjectByidNoUser($getProject['data_id']);
    $projsite = isset($myproject[0]['sitecode']) ? $myproject[0]['sitecode'] : '';
}

if(isset($ezf_id)){
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
    $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getContentForm($modelEzf->ezf_table, ['sitecode' => $projsite]);
}else{
    $model_menu=null;
}

$form = ActiveForm::begin([
            'id' => 'frm_content',
            'action' => 'project-home/update-content'
        ]);
echo Html::hiddenInput('data_id', isset($model_menu['id'])?$model_menu['id']:'');

$settings = [
    'minHeight' => 500,
    'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
    'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
    'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
    'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
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
];
echo \vova07\imperavi\Widget::widget([
    'name' => 'web_content',
    'value' => isset($model_menu['menu_content'])?$model_menu['menu_content']:'<h4>Project home index</h4>',
    'settings' => $settings,
]);
echo Html::submitButton('Update', ['class' => 'btn btn-primary pull-right btn_update_content']);

ActiveForm::end();

$this->registerJS("

$('form#frm_content').on('beforeSubmit', function(e) {

var \$form = $(this);
var formData = new FormData($(this)[0]);

$.ajax({
url: \$form.attr('action'),
type: 'POST',
data: formData,
dataType: 'JSON',
enctype: 'multipart/form-data',
processData: false,  // tell jQuery not to process the data
contentType: false,   // tell jQuery not to set contentType
success: function (result) {
console.log(result);
    if(result.status == 'success') {
    " . SDNoty::show('result.message', 'result.status') . "
    } else {
    " . SDNoty::show('result.message', 'result.status') . "
    }
},
});

return false;
});

");

?>