<?php
 
namespace common\modules\user\classes;
use Yii;
use kartik\widgets\Select2;
use yii\helpers\Url;
class CNSitecode {
     public static function getSiteCodeForm($form, $model, $attri="sitecode",$titleLable = 'Sitecode',$option = []){
//        $sql="SELECT site_name, site_detail FROM zdata_sitecode WHERE site_name=:site_name";
//        $initSite = Yii::$app->db->createCommand($sql,[':site_name'=>$model['sitecode']])->queryOne();
        
      $initSite = (new \yii\db\Query())
              ->select(['site_name','site_detail'])
              ->from('zdata_sitecode')
              ->where('site_name=:site_name', [':site_name'=>$model['sitecode']])
              ->one();
       echo $form->field($model, 'sitecode')->widget(Select2::classname(), [
           'value'=>$initSite['site_name'],
           'initValueText'=>$initSite['site_detail'],
           'name'=>'sitecode', 
           'options'=>['placeholder'=> Yii::t('rbac-admin','Select Site')],
           'pluginOptions' => [
               'allowClear' => isset($option["allowClear"]) ? $option["allowClear"] : true,
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
       ])->label(Yii::t('chanpan',$titleLable));
     }
     
     public static function getSitecodeByCondition($select, $condition, $limit = "") {
        
        $data = (new \yii\db\Query())
                ->select($select)
                ->from('zdata_sitecode')
                ->where($condition)
                ->all();

        return isset($data) ? $data : ''; 
    }
     
      public static function getSiteCodeSelect2SingleAjaxBySite($name, $label, $sitecodeId) {
        
         
        $data = CNSitecode::getSitecodeByCondition(['site_name','site_detail'], "rstat <> 3 AND rstat <> 0");
        $select2 = "<div class='form-group'>";
        $select2 .= '<label>' . $label . '</label>';
        $select2 .= Select2::widget([
                    'name' => $name,
                    'value' => $data['id'],
                    'initValueText' => $data['text'],
                    'options' => ['placeholder' => 'ค้นหา  ' . $label],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/ezforms2/select-site-single/get-site']),
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
                    ],
        ]);
        $select2 .= "</div>";
        return $select2;
    }
    
    public static function getSiteValue($site=''){
        if($site == ''){
            $sitecode = isset(Yii::$app->user->identity->profile->sitecode)?Yii::$app->user->identity->profile->sitecode:'';  
        }else{
            $sitecode = $site;
        }       
        $data = (new \yii\db\Query())
                ->select('*')
                ->from('zdata_sitecode')
                ->where(['site_name'=>$sitecode])
                ->one();
//        \appxq\sdii\utils\VarDumper::dump($sitecode);
        $site_detail = isset($data['site_detail']) ? $data['site_detail'] : '';
        $site_name = isset($data['site_name']) ? $data['site_name'] : '';
        return $site_detail." ({$site_name})"; 
    }
    public static function getSiteCodeCurrent(){
        return isset(Yii::$app->user->identity->profile->sitecode)?Yii::$app->user->identity->profile->sitecode:'';
    }
}
