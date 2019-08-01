<?php
namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\models\Ezform;
/**
 * Description of DataListStore
 *
 * @author appxq
 */
class DataListStore {
    //put your code here
//    $params = [
//    'field'=>$field,
//    'data'=>$model
//];
    
    public static function getEzFormListGroupByType($params=[]) {
        
        $model1 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere(Ezform::tableName().'.created_by=:user_id  ', [':user_id' => Yii::$app->user->id])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model2 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere(' ('.Ezform::tableName().'.ezf_id in (SELECT '.Ezform::tableName().'.ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)) ', [':user_id' => Yii::$app->user->id])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model3 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere(' (shared = 3 AND xsourcex=:xsourcex) OR (shared = 2 AND '.Ezform::tableName().'.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model4 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('shared=1 ')
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $out = [];
        $i = 0;
        $out["My owner"] = [];
        $array_add = [];
        foreach ($model1 as $value) {
           $array_add[$value['ezf_id']] =$value["ezf_name"];
        }
        
        $out["My owner"] = $array_add;

        $i++;
        $out["Co-creator"]= [];
        $array_add = [];
        foreach ($model2 as $value) {
            $array_add[$value['ezf_id'] ] = $value["ezf_name"];
        }
        
        $out["Co-creator"] = $array_add;

        $i++;
        $out["Assigned to me"]= [];
        $array_add = [];
        foreach ($model3 as $value) {
            $array_add[$value['ezf_id'] ] =$value["ezf_name"];
        }

        $out["Assigned to me"] = $array_add;
        
        $i++;
        $out["Public"]= [];
        $array_add = [];
        foreach ($model4 as $value) {
            $array_add[$value['ezf_id']] = $value["ezf_name"];
        }
        
        $out["Public"] = $array_add;

        //return json_encode($out);
        
        return $out;
    }
    
    public static function getEzFormCRFList($params=[]) {
               $model1 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere(Ezform::tableName().'.created_by=:user_id  ', [':user_id' => Yii::$app->user->id])
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model2 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('('.Ezform::tableName().'.ezf_id in (SELECT '.Ezform::tableName().'.ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)) ', [':user_id' => Yii::$app->user->id])
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model3 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('(shared = 3 AND xsourcex=:xsourcex) OR (shared = 2 AND '.Ezform::tableName().'.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model4 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('shared=1 ')
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $out = [];
        $i = 0;
        $out["My owner"] = [];
        $array_add = [];
        foreach ($model1 as $value) {
           $array_add[$value['ezf_id']] =$value["ezf_name"];
        }
        
        $out["My owner"] = $array_add;

        $i++;
        $out["Co-creator"]= [];
        $array_add = [];
        foreach ($model2 as $value) {
            $array_add[$value['ezf_id'] ] = $value["ezf_name"];
        }
        
        $out["Co-creator"] = $array_add;

        $i++;
        $out["Assigned to me"]= [];
        $array_add = [];
        foreach ($model3 as $value) {
            $array_add[$value['ezf_id'] ] =$value["ezf_name"];
        }

        $out["Assigned to me"] = $array_add;
        
        $i++;
        $out["Public"]= [];
        $array_add = [];
        foreach ($model4 as $value) {
            $array_add[$value['ezf_id']] = $value["ezf_name"];
        }
        $out["Public"] = $array_add;

        //return json_encode($out);
        return $out;
    }
    
}
