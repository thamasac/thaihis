<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use appxq\sdii\utils\SDdate;
use backend\modules\subjects\classes\SubjectManagementQuery;

use Yii;
class TimelineMilestoneController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $module_id = Yii::$app->request->get('module_id');
        $dataid = Yii::$app->request->get('dataid');
        $project_id = Yii::$app->request->get('project_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $sub_filter = Yii::$app->request->get('sub_filter');
        $filter = ['task_type' => 'milestone'];
        if($sub_filter && !empty($sub_filter)){
            $filter['parent']=$sub_filter;
        }
        if($project_id && !empty($project_id)){
            $filter['target']=$project_id;
        }
        $sent_timeline = SubjectManagementQuery::GetTableData('pms_task_target', $filter);
        $subtaskQuery = SubjectManagementQuery::GetTableQuery('pms_task_target', ['target'=>$project_id]);
        
        $subtask_list = [];
        
        foreach ($subtaskQuery->all() as $value){
            $mileData = SubjectManagementQuery::GetTableQuery('pms_task_target', ['parent'=>$value['id'],'task_type'=>'milestone'])->one();
            if($mileData && count($mileData) > 0){
                if($value['priority'] == '2'){
                    $subtask_list[]=$value;
                }
            }
        }
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $sent_timeline,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => ['start_date'=>SORT_DESC],
                'attributes' => ['task_name','start_date','finish_date','actual_date','respons_person'],

            ],

        ]);

        $itemTL = [];
        $count = 0;
        foreach ($sent_timeline as $key => $value) {
            $datenow = date('Y-m-d H:i:s');
            $startDate = isset($value['start_date'])?date('Y-m-d H:i:s', strtotime($value['start_date'])):'';
            $finishDate = isset($value['finish_date'])?date('Y-m-d H:i:s', strtotime($value['start_date'])):'';
            $actualDate = isset($value['actual_date'])?date('Y-m-d H:i:s', strtotime($value['actual_date'])):'';
            $respons_person = isset($value['respons_person'])?json_decode($value['respons_person']):'';


            $itemTL[$count]['id'] = $value['dataid'];

            if(!isset($respons_person)){
                $user_assign= [];
                $num = 0;
                for($i=0;$i<2;$i++){
                    $uname = \cpn\chanpan\classes\CNUser::GetUserNcrcById($respons_person[$i]);
                    $user_assign[$num] = $uname['profile']['firstname'];
                    $num++;
                }

                $user_assign = implode(",", $user_assign);
                if(count($respons_person)>2){
                    $user_assign .=  ',...';
                }
            }else{
                $user_assign = '';
            }

            $start_date = isset($value['start_date'])?SDdate::mysql2phpDateTime($startDate):'';
            $end_date = isset($value['finish_date'])?SDdate::mysql2phpDateTime($finishDate):'';
            $act_date = isset($value['finish_date'])?SDdate::mysql2phpDateTime($actualDate):'';
            $itemTL[$count]['content'] = '<b>'.$value['task_name'].'</b> ['.$user_assign.']';


            if( !empty($actualDate) && ($actualDate > $datenow) ){
                $itemTL[$count]['content'] .= '<br><b>Start Date : </b>'.$start_date.' <br><b> Actual Date : </b>'.$act_date;
                $itemTL[$count]['start'] = $actualDate;
                $itemTL[$count]['className'] = 'warning';
            }elseif (empty($actualDate) && ($startDate < $datenow)){
                $itemTL[$count]['content'] .= '<br><b>Start Date : </b>'.$start_date;
                $itemTL[$count]['start'] = $startDate;
                $itemTL[$count]['className'] = 'danger';
            }elseif (!empty($actualDate)  && ($actualDate <= $datenow)) {
                $itemTL[$count]['content'] .= '<br><b>Actual Date : </b>'.$act_date;
                $itemTL[$count]['start'] = $actualDate;
                $itemTL[$count]['className'] = 'success';
            }elseif (empty($actualDate) && ($startDate >= $datenow)){
                $itemTL[$count]['content'] .= '<br><b>Start Date : </b>'.$start_date;
                $itemTL[$count]['start'] = $startDate;
                $itemTL[$count]['className'] = 'default';
            }
            $count++;
        }

        return $this->renderAjax('index', [
            'dataProvider'=>$dataProvider,
            'itemTL'=>$itemTL,
            'dataid'=>$dataid,
            'subtask_list'=>$subtask_list,
            'sub_filter'=>$sub_filter,
            'reloadDiv'=>$reloadDiv,
            'project_id'=>$project_id

        ]);
    }
}