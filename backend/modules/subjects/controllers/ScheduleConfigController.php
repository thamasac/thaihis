<?php

namespace backend\modules\subjects\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\Ezform;
use yii\helpers\Url;
use backend\modules\subjects\classes\ExportexcelFunc;

class ScheduleConfigController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');

        return $this->renderAjax('index', [
                    'widget_id' => $widget_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'module_id' => $module_id,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionVisitConfig() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');

        return $this->renderAjax('visit-config', [
                    'widget_id' => $widget_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'module_id' => $module_id,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionGridGroup() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');

        $scheduleData = SubjectManagementQuery::getWidgetById($schedule_id);
        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($scheduleData['options']);
        $ezformGroup = EzfQuery::getEzformOne($scheduleOptions['group_ezf_id']);
        $groupQuery = SubjectManagementQuery::GetTableQuery($ezformGroup);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $groupQuery,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                //'create_date' => SORT_DESC
                ],
            ]
        ]);

        return $this->renderAjax('grid-group', [
                    'module_id' => $module_id,
                    'widget_id' => $widget_id,
                    'options' => $scheduleOptions,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'group_ezf_id' => $scheduleOptions['group_ezf_id'],
                    'reloadDiv' => $reloadDiv,
                    'dataProvider' => $dataProvider,
                    'schedule_id' => $schedule_id,
                    'procedure_id'=>$procedure_id,
        ]);
    }

    public function actionGridVisit() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $export = Yii::$app->request->get('export');

        $scheduleData = SubjectManagementQuery::getWidgetById($schedule_id);
        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($scheduleData['options']);

        if (isset($export) && $export == true) {
            $vistQuery = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
            $fileName = ExportexcelFunc::ExportExcelTbdata('Visit Schedule', 'Visit Schedule', $vistQuery[0], $vistQuery);
            $this->redirect(Yii::getAlias('@web/print/').$fileName);
        } else {
            $vistQuery = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);

            $dataProvider = new \yii\data\ArrayDataProvider([
                'key' => 'id',
                'allModels' => $vistQuery,
                'pagination' => [
                    'pageSize' => 50,
                //'route' => '/ezforms2/fileinput/grid-update',
                ],
                'sort' => [
                    'attributes' => ['visit_name', 'group_name'],
                ],
            ]);


            return $this->renderAjax('grid-visit', [
                        'module_id' => $module_id,
                        'widget_id' => $widget_id,
                        'options' => $scheduleOptions,
                        'user_create' => $user_create,
                        'user_update' => $user_update,
                        'reloadDiv' => $reloadDiv,
                        'group_ezf_id' => $scheduleOptions['group_ezf_id'],
                        'dataProvider' => $dataProvider,
                        'schedule_id' => $schedule_id,
            ]);
        }
    }
    
    public function actionExportVisitView(){
        return $this->renderAjax('export-view');
    }

    public function actionExportVisitSchedule() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $schedule_id = "1520793023009873800";

        //=== Export Data ===
        $url = Url::to(['/subjects/schedule-config/grid-visit',
                    'schedule_id' => $schedule_id,
                    'export' => true,
        ]);

        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

        $result = [
            'status' => 'success',
            'action' => 'report',
            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
            'html' => $html,
        ];

        return \yii\helpers\Json::encode($result);
    }

    public function actionGetEzforms($q = null) {
        $params = [];
        $model1 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere(Ezform::tableName().'.created_by=:user_id  ', [':user_id' => Yii::$app->user->id])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"' )
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model2 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('shared = 0 AND ('.Ezform::tableName().'.ezf_id in (SELECT '.Ezform::tableName().'.ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)) ', [':user_id' => Yii::$app->user->id])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model3 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('(shared = 1 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared = 2 AND '.Ezform::tableName().'.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model4 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->where('status =1')
                ->andWhere('public_listview=1 ')
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $out = [];
        $i = 0;
        $out["results"][$i] = ['text' => 'My own'];
        $out["results"][$i]['children'] = [];
        foreach ($model1 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Co-creator'];
        $out["results"][$i]['children'] = [];
        foreach ($model2 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Assigned to me'];
        $out["results"][$i]['children'] = [];
        foreach ($model3 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Public'];
        $out["results"][$i]['children'] = [];
        foreach ($model4 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        return json_encode($out);
    }
    
    public function actionGetEzformsSubjectProfile($q = null) {
        $profile_ezf = "1521941065093085100";
        $params = [];
        $model1 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->innerJoin('ezform_fields','ezform_fields.ezf_id='.Ezform::tableName().'.ezf_id')
                ->where('status =1')
                ->andWhere(Ezform::tableName().'.created_by=:user_id  ', [':user_id' => Yii::$app->user->id])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"' )
                ->andWhere(['ezf_crf'=>'1'])
                ->andWhere(['ezform_fields.ref_ezf_id'=>'1521941065093085100'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model2 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->innerJoin('ezform_fields','ezform_fields.ezf_id='.Ezform::tableName().'.ezf_id')
                ->where('status =1')
                ->andWhere('shared = 0 AND ('.Ezform::tableName().'.ezf_id in (SELECT '.Ezform::tableName().'.ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)) ', [':user_id' => Yii::$app->user->id])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->andWhere(['ezform_fields.ref_ezf_id'=>'1521941065093085100'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model3 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->innerJoin('ezform_fields','ezform_fields.ezf_id='.Ezform::tableName().'.ezf_id')
                ->where('status =1')
                ->andWhere('(shared = 1 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared = 2 AND '.Ezform::tableName().'.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->andWhere(['ezform_fields.ref_ezf_id'=>'1521941065093085100'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $model4 = Ezform::find()
                ->select([Ezform::tableName().'.ezf_id','ezf_name'])->distinct()
                ->innerJoin('ezform_fields','ezform_fields.ezf_id='.Ezform::tableName().'.ezf_id')
                ->where('status =1')
                ->andWhere('public_listview=1 ')
                ->andWhere(' ezf_name LIKE "%' . $q . '%"')
                ->andWhere(['ezf_crf'=>'1'])
                ->andWhere(['ezform_fields.ref_ezf_id'=>'1521941065093085100'])
                ->orderBy(Ezform::tableName().'.created_at DESC')
                ->all();

        $out = [];
        $i = 0;
        $out["results"][$i] = ['text' => 'My own'];
        $out["results"][$i]['children'] = [];
        foreach ($model1 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Co-creator'];
        $out["results"][$i]['children'] = [];
        foreach ($model2 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Assigned to me'];
        $out["results"][$i]['children'] = [];
        foreach ($model3 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        $i++;
        $out["results"][$i] = ['text' => 'Public'];
        $out["results"][$i]['children'] = [];
        foreach ($model4 as $value) {
            $out["results"][$i]['children'][] = ['id' => $value['ezf_id'], 'text' => $value["ezf_name"]];
        }

        return json_encode($out);
    }
    
    public static function initEzformIcf($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        
        if(isset($code) && !empty($code)){
            
            $sql = "SELECT ezf_id,ezf_name FROM `ezform` WHERE `ezf_id`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['ezf_name'];
        }
        
        return $str;
    }

}
