<?php
 
namespace common\modules\user\classes;
use Yii;
use kartik\widgets\Select2;
use yii\helpers\Url;
class CNDepartment {
    public static function getDepartmentValue($user_id){
        $user_id = isset($user_id) ? $user_id : '';
        $data = (new \yii\db\Query())->select('*')->from('profile')->where(['user_id'=>$user_id])->one();
        
        $initSite = (new \yii\db\Query())
            ->select(['unit_name'])
            ->from("zdata_working_unit")
            ->where(['id'=>$data['department']])
            ->andWhere('rstat not in(0,3)')
            ->one();
        return isset($initSite['unit_name']) ? $initSite['unit_name'] : '';
    }
    public static function getDepartmentForm($form, $model, $attri="department",$titleLabel = "Department"){
        
        $initSite = (new \yii\db\Query())
        ->select(['id', 'unit_name as name'])
                ->from("zdata_working_unit")
                ->where(['id'=>$model['department']])
                ->andWhere('rstat not in(0,3)')
                ->one();
                
        return $form->field($model, $attri)->widget(Select2::classname(), [
            'value' => isset($initSite['id']) ? $initSite['id'] : '99', //id
            'initValueText' => isset($initSite['name']) ? $initSite['name'] : 'Default(99)', //name
            'name' => 'department',
            'options' => ['placeholder' => \Yii::t('chanpan','Select Department'), 'id'=> 'department_'.time()],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/select-department/get-department']),
                    'dataType' => 'json',
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
            ],
        ])->label(Yii::t('chanpan', $titleLabel));
    }
    public static function getDepartmentFormNotModel($name, $label, $departmentId){
        $initSite = (new \yii\db\Query())
        ->select(['id', 'unit_name as name'])
                ->from("zdata_working_unit")
                ->where(['id'=>$departmentId])
                ->andWhere('rstat not in(0,3)')
                ->one();
        $select2 = "<div class='form-group'>";
        $select2 .= '<label>' . $label . '</label>';
        $select2 .= Select2::widget([
                    'name' => isset($name) ? $name : '',
                    'value' => isset($initSite['id']) ? $initSite['id'] : '',
                    'initValueText' => isset($initSite['name']) ? $initSite['name'] : '',
                    'options' => ['placeholder' => 'ค้นหา  ' . isset($label) ? $label : ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => Url::to(['/ezforms2/select-department/get-department']),
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
}
