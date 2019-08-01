<?php

namespace common\modules\user\classes;

use Yii;
use kartik\widgets\Select2;
use yii\helpers\Url;

class SiteCodeFunc {

    /**
     * @param string $site_name sitecode
     * @return string sitecode
     */
    public static function getSiteCodeValue($site_name) {
        \backend\modules\ezforms2\classes\EzfAuth::auth()->getSite(isset($module_id) ? $module_id : '', isset($user_id) ? $user_id : '');
        $sql = "SELECT site_name, site_detail FROM zdata_sitecode WHERE site_name=:site_name";
        $initSite = Yii::$app->db->createCommand($sql, [':site_name' => $site_name])->queryOne();
        $sitecode_title = isset($initSite['site_detail']) ? $initSite['site_detail'] : '';
        return '<a style="text-decoration:none;" data-toggle="tooltip" title="' . $sitecode_title . '">' . $site_name . '</a></script>';
    }

    public static function getAuthList() {
        try {
            $auth_str = (new \yii\db\Query())->select('*')->from('auth_item')->where(['type' => '1'])->all();
            return \yii\helpers\ArrayHelper::map($auth_str, 'name', 'description');
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }

    public static function getAuthListByName($name) {
        try {

            $auth_str = (new \yii\db\Query())
                    ->select('*')
                    ->from('auth_item')
                    ->where('name=:name AND type=1', [':name' => $name])
                    ->one();
            return isset($auth_str) ? $auth_str : '';
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }

    public static function getAuthAssign($user_id) {
        try {
            $auth_str = (new \yii\db\Query())->select('*')->from('auth_assignment')->where(['user_id' => $user_id])->all();
            return \yii\helpers\ArrayHelper::map($auth_str, 'item_name', 'item_name');
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }
    public static function get_switch_site($form, $model,$disable = false, $name) {
        
        $site_switch = \appxq\sdii\utils\SDUtility::string2Array($model['site_switch']);
       // \appxq\sdii\utils\VarDumper::dump($site_switch);
        
        $data_site_arr = (new \yii\db\Query())
                ->select('*')->from('zdata_sitecode')->where(['site_name'=>$site_switch])->all();
        $model->site_switch = \yii\helpers\ArrayHelper::map($data_site_arr, 'site_name','site_name');
        $init_site_text = \yii\helpers\ArrayHelper::map($data_site_arr, 'site_detail','site_detail');
        
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        echo $form->field($model, 'site_switch')->widget(Select2::classname(), [
           // 'options' => ['placeholder' => Yii::t('chanpan', 'Select Site Code')],
            'initValueText' =>$init_site_text,//['site_name','site_detail'],
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/select-site-single/get-site']),
                    'dataType' => 'json',
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
            ],
        ])->label(Yii::t('chanpan', 'Switch Site'));
    }
    public static function getSiteCode($form, $model,$disable = false) {
        $sql = "SELECT site_name, site_detail FROM zdata_sitecode WHERE site_name=:site_name";
        $initSite = Yii::$app->db->createCommand($sql, [':site_name' => $model['sitecode']])->queryOne();
        $site_name = isset($initSite['site_name']) ? $initSite['site_name'] : '';
        $site_detail = isset($initSite['site_detail']) ? $initSite['site_detail'] : '';
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $idModal = 'modal-sitecode-' . $id;
        $modal = '<div id="' . $idModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>';
        echo $form->field($model, 'sitecode')->widget(Select2::classname(), [
            'value' => $site_name,
            'initValueText' => $site_detail . " ({$site_name}) ",
            'name' => 'sitecode',
            'disabled' => $disable,
            'options' => ['placeholder' => Yii::t('chanpan', 'Select Site Code')],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/select-site-single/get-site']),
                    'dataType' => 'json',
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
            ],
        ])->label(Yii::t('chanpan', 'Site'));
        $view = Yii::$app->getView();
        \backend\modules\ezforms2\assets\EzfGenAsset::register($view);
        \backend\modules\ezforms2\assets\EzfColorInputAsset::register($view);
        \backend\modules\ezforms2\assets\DadAsset::register($view);
        \backend\modules\ezforms2\assets\EzfToolAsset::register($view);
        \backend\modules\ezforms2\assets\EzfTopAsset::register($view);
        \backend\modules\ezforms2\assets\ListdataAsset::register($view);
        $view->registerJs("
                    var hasMyModal = $('body').has('#$idModal').length;
                    var hasDiv = $('body').has('#ezf-main-box').length;
                    
                    if (!hasDiv) {
                        $('.page-column').append(`<div id=\"ezmodule-main-app\" class=\"ezmodule-view\">
                            <div class=\"modal-body\">
                                <div id=\"ezf-main-box\">
                                    <div id=\"ezf-modal-box\">
                $modal
                                    </div>
                                </div>
                            </div>
                        </div>
                        `);
                    } else {
                        if (!hasMyModal) {
                            $('#ezf-modal-box').append(`
                $modal 
                `);

                        }
                    }
                    
                    $('#profile-sitecode').on('change',function(){
                        if($(this).val() == '-9999'){
                        $(this).val('').trigger('change');
                            $('#$idModal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                            $('#$idModal').modal('show')
                            .find('.modal-content')
                            .load('/ezforms2/ezform-data/ezform?ezf_id=1520514351069551000&modal=$idModal&reloadDiv=&target=&dataid=&targetField=&version=&db2=0&initdata=');
                        }
                    });
                ");
    }

}
