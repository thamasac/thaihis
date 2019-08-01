<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use backend\modules\ezforms2\models\EzformSearch;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use appxq\sdii\helpers\SDHtml;

class DataListsController extends Controller {

    public function actionIndex() {
        $ezf_id = Yii::$app->request->get('ezf_id', 0);
        $status = Yii::$app->request->get('status', -1);
        $target = Yii::$app->request->get('target', '');
        $view = Yii::$app->request->get('view', 2);
        
        $searchModel = new EzformSearch();
        $dataProvider = $searchModel->searchDataList(Yii::$app->request->queryParams);
        \backend\modules\manageproject\classes\CNFunc::addLog("View EzEntry | Data Entry ezf_id= {$ezf_id}");
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'ezf_id' =>$ezf_id,
                    'status' => $status,
                    'target' => $target,
                    'view' => $view,
        ]);
    }
    
    public function actionList() {
        $searchModel = new EzformSearch();
        $tab =  Yii::$app->request->get('tab', '1');
        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);
        $modelFav = \backend\modules\ezforms2\models\EzformFavorite::find()
                    ->where('userid=:userid', [':userid'=> Yii::$app->user->id])->all();
        \backend\modules\manageproject\classes\CNFunc::addLog("View List Tab={$tab}");
        return $this->renderAjax('_list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider, 'tab' => $tab,
                    'modelFav'=>$modelFav,
        ]);
    }

    public function actionFavorite($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $model = \backend\modules\ezforms2\models\EzformFavorite::find()
                    ->where('ezf_id=:ezf_id and userid=:userid', [':userid'=> Yii::$app->user->id, ':ezf_id'=>$ezf_id])->one();
            if($model){
                $model->delete();
            } else {
                $model = new \backend\modules\ezforms2\models\EzformFavorite();
                $model->ezf_id = $ezf_id;
                $model->userid = Yii::$app->user->id;
                $model->forder = EzfQuery::getOrderFav(Yii::$app->user->id);
                \backend\modules\manageproject\classes\CNFunc::addLog("Favorite form ezf_id={$ezf_id} ". json_encode($model));
                if($model->save()){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $ezf_id,
                    ];

                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                        'data' => $ezf_id,
                    ];

                    return $result;
                }
            }
            
            
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionOrderUpdate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $position = isset($_POST['position'])?$_POST['position']:[];
           
	    $sql = '';
	    foreach ($position as $key => $ezf_id) {
		$order = $key;
                try {
                    if($ezf_id!=''){
                         
                        $r = Yii::$app->db->createCommand()
                            ->update('ezform_favorite', ['forder'=>$order], '`ezf_id`=:ezf_id AND userid=:userid', [':ezf_id'=>(int)$ezf_id, ':userid'=> Yii::$app->user->id])
                            ->execute();
                    }
                }
                catch (\yii\db\Exception $e)
                {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
	    }
	}
    }
    
    public function actionExport($ezf_id)
    {
        
        
        $labelEnable = Yii::$app->request->get('labelEnable', 0);
        $fSystem = Yii::$app->request->get('fsys', 0);
        
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        
        $fileName = 'backup_data_'. \yii\helpers\Inflector::slug($modelEzf->ezf_name).date('_Y_m_d').'.xlsx';
        
        Yii::$app->session['show_varname']=0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        //Yii::$app->session['ezform'] = $modelEzf->attributes;

        $userProfile = Yii::$app->user->identity->profile;

        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        $model = \backend\modules\ezforms2\classes\EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        
        $modelSave = new \backend\modules\ezforms2\models\TbdataAll();
        $modelSave->setTableName($modelEzf->ezf_table);
        
        $sTarget = Yii::$app->request->get('EzformTarget', NULL);
        $params = [];
        $where = '';
        if(isset($sTarget)){
            if(isset($sTarget['ezf_id']) && !empty($sTarget['ezf_id'])){
                $where .= ' AND ezf_id=:ezf_id';
                $params[':ezf_id'] = $sTarget['ezf_id'];
            }
            if(isset($sTarget['user_update']) && !empty($sTarget['user_update'])){
                $where .= ' AND user_update=:user_update';
                $params[':user_update'] = $sTarget['user_update'];
            }
            if(isset($sTarget['xsourcex']) && !empty($sTarget['xsourcex'])){
                $where .= ' AND xsourcex=:xsourcex';
                $params[':xsourcex'] = $sTarget['xsourcex'];
            }
            if(isset($sTarget['rstat']) && !empty($sTarget['rstat'])){
                $where .= ' AND rstat=:rstat';
                $params[':rstat'] = $sTarget['rstat'];
            }
            if(isset($sTarget['target_id']) && !empty($sTarget['target_id'])){
                $where .= ' AND target=:target';
                $params[':target'] = $sTarget['target_id'];
            }
        }
        
        $modelEvent = EzfQuery::getEventFields($ezf_id);
        $modelSpecial;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelSpecial = EzfQuery::findSpecialOne($ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelSpecial = true;
                }
            }
        }
        if (isset($modelSpecial) || $modelEzf['public_listview'] == 2) {
            $where .= ' AND xsourcex = :site';
            $params[':site'] = Yii::$app->user->identity->profile->sitecode;
        }
        
        if ($modelEzf['public_listview'] == 3) {
            $where .= ' AND xdepartmentx = :unit';
            $params[':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($modelEzf['public_listview'] == 0) {
            $where .= ' AND user_create=:created_by';
            $params[':created_by'] = Yii::$app->user->id;
        }
        
        $modelSave = $modelSave->find()->where('rstat <> 0 AND rstat <> 3 '.$where, $params)->all();
        
        if($modelSave){
            $model = $modelSave;
        } else {
            $model->init();
        }
        $columns = EzfQuery::showColumn($modelEzf->ezf_table);
        $columns = ArrayHelper::getColumn($columns, 'Field');
        $label = ArrayHelper::map($modelFields, 'ezf_field_name', 'ezf_field_label');
        
        if($fSystem==1){
            foreach ($columns as $key => $value) {
                if(in_array($value, ['sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'xsourcex', 'xdepartmentx', 'sys_lat', 'sys_lng', 'error', 'rstat', 'user_create', 'user_update', 'ezf_version'])){
                    unset($columns[$key]);
                }
            }
        }
        
        $headers = NULL;
        foreach ($columns as $value) {
            if($labelEnable){
                $headers[$value] = isset($label[$value])?$label[$value]:$value;
            } else {
                $headers[$value] = $value;
            }
        }
        \backend\modules\manageproject\classes\CNFunc::addLog("Export form ezf_id={$ezf_id} ".\appxq\sdii\utils\SDUtility::array2String($modelEzf));
	$export = \appxq\sdii\widgets\SDExcel::export([
            'fileName'=>$fileName,
            'savePath'=> Yii::getAlias('@backend/web/print'),
            'format'=>'Xlsx',
            'asAttachment'=>false,
            'models' => $model, 
            'columns' => $columns, 
            'headers' => $headers, 
        ]);
        
        $this->redirect(Yii::getAlias('@web/print/').$fileName);
    }
}

?>
