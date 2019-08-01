<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfForm;
use yii\web\UploadedFile;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformCoDev;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformAssign;
use backend\modules\ezforms2\models\EzformCondition;

/**
 * EzformController implements the CRUD actions for Ezform model.
 */
class EzformController extends Controller {

    public function behaviors() {
        return [
            /* 	    'access' => [
              'class' => AccessControl::className(),
              'rules' => [
              [
              'allow' => true,
              'actions' => ['index', 'view'],
              'roles' => ['?', '@'],
              ],
              [
              'allow' => true,
              'actions' => ['view', 'create', 'update', 'delete', 'deletes'],
              'roles' => ['@'],
              ],
              ],
              ], */
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Ezform models.
     * @return mixed
     */
    public function actionIndex() {
        \backend\modules\manageproject\classes\CNFunc::addLog('View Ezform All');
        $searchModel = new EzformSearch();
        $tab = Yii::$app->request->get('tab', 1);
        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);

        //$userlist = ArrayHelper::map(EzfQuery::getIntUserAll(), 'id', 'text');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider, 'tab' => $tab
        ]);
    }
    
    public function actionIndexCrf() {
        if (Yii::$app->getRequest()->isAjax) {
            $tab = 9;
            $searchModel = new EzformSearch();
            
            $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);
            
            return $this->renderAjax('_crf', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider, 'tab' => $tab
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionEr($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            \backend\modules\manageproject\classes\CNFunc::addLog("View ER Diagram ezf_id= {$ezf_id}");
            return $this->renderAjax('_er', [
                        'ezf_id' => $ezf_id,
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionSrd($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            \backend\modules\manageproject\classes\CNFunc::addLog("View Staple Relation Diagram ezf_id= {$ezf_id}");
            return $this->renderAjax('_srd', [
                        'ezf_id' => $ezf_id,
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionOrganizeChart() {
        if (Yii::$app->getRequest()->isAjax) {
            return $this->renderAjax('_organize_chart', [
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Displays a single Ezform model.
     * @param string $id
     * @return mixed
     */
    public function actionView() {
        
        return $this->render('view', [
        ]);
    }
    
    public function actionViewTable($id) {
        $table = 'zdata_view_table';
        $data = [];
        $dataConfig = Yii::$app->db->createCommand("SELECT * FROM $table WHERE id=:id", [':id'=>$id])->queryOne();
        if($dataConfig){
            $ezf_id = $dataConfig['cform'];
            $fields = SDUtility::string2Array($dataConfig['cfields']);
                    
            if(isset($dataConfig['cformt']) && $dataConfig['cformt']==2)  {//Wide
                
            } else {//Long
                $field_target = EzfQuery::getTargetOne($ezf_id);
                $ezf_id_main = 0;
                if($field_target){
                    if(isset($field_target['parent_ezf_id']) && !empty($field_target['parent_ezf_id'])){
                        $ezf_id_main = $field_target['parent_ezf_id'];
                    }
                } else {
                    $ezf_id_main = $ezf_id;
                }

                $ezform = EzfQuery::getEzformById($ezf_id);
                $ezformParent = EzfQuery::getEzformById($ezf_id_main);
                if($ezform && $ezformParent){
                    $query = new \yii\db\Query();
                    $query->from($ezformParent['ezf_table']);

                    $select = [];
                    $alias_select = [];
                    $ezformTmpUnipue = [];
                    $ezformTmp[$ezformParent['ezf_id']] = $ezformParent['ezf_table'];

                    foreach ($fields as $key => $field) {
                        $obj_item = explode(':', $field['select']);
                        $form = isset($obj_item[0])?$obj_item[0]:0;
                        $table = isset($obj_item[1])?$obj_item[1]:'';
                        $field_name = isset($obj_item[2])?$obj_item[2]:'';

                        //select
                        if(isset($alias_select[$field_name])){
                           $select[] = "`$table`.`$field_name` AS {$field_name}_{$alias_select[$field_name]}";  
                        } else {
                            $select[] = "`$table`.`$field_name`";
                        }

                        $alias_select[$field_name] = isset($alias_select[$field_name])?$alias_select[$field_name]+1:2;

                        //join
                        if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $form) {

                        } else {
                            if (!in_array($form, $ezformTmpUnipue)) {


                                $modelTarget = EzfQuery::getTargetOne($form);
                                $refFormCond = SDUtility::string2Array($modelTarget['ref_form']);
                                $refFormTarget = [];
                                if (isset($targetField)) {
                                    $refFormTarget = SDUtility::string2Array($targetField['ref_form']);
                                }

                                $lvlTarget = count($refFormTarget);
                                $lvlCond = count($refFormCond);


                                if ($lvlTarget < $lvlCond) {//ต่ำกว่าฟอร์มตั้งต้น
                                    if (isset($refFormCond[$ezform['ezf_id']])) {
                                        $joinField = $refFormCond[$ezform['ezf_id']];
                                        $modelEzfCond = EzfQuery::getFormTableName($form);
                                        $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                        $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                    } elseif ($modelTarget['ref_ezf_id'] == $ezform['ezf_id']) {// กรณี ref กัน 1 lvl
                                        $joinField = $modelTarget['ezf_field_name'];
                                        $modelEzfCond = EzfQuery::getFormTableName($form);
                                        $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                        $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                    }
                                } elseif ($lvlTarget == $lvlCond) {
                                    $modelEzfCond = EzfQuery::getFormTableName($form);
                                    if (isset($modelTarget)) {//ต่ำกว่าฟอร์มตั้งต้น
                                        if ($ezform['ezf_id'] == $modelTarget['ref_ezf_id']) {
                                            $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.{$modelTarget['ezf_field_name']} = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");
                                            $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                        }
                                    } else {//สูงกว่าฟอร์มตั้งต้น
                                        if (isset($targetField)) {
                                            if ($form == $targetField['ref_ezf_id']) {
                                                $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.{$targetField['ezf_field_name']} AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");
                                                $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                            }
                                        }
                                    }
                                } elseif ($lvlTarget > $lvlCond) {//สูงกว่าฟอร์มตั้งต้น
                                    if (isset($refFormTarget[$form])) {
                                        $joinField = $refFormTarget[$form];
                                        $modelEzfCond = EzfQuery::getFormTableName($form);
                                        $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.$joinField AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                        $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                    }
                                }

                                $ezformTmpUnipue[] = $form;
                            }
                        }

                    }

                    $query->select($select);
                    $query->where("{$ezformParent['ezf_table']}.rstat not in(0,3)");
                    $data = $query->createCommand()->queryAll();
                }
            }   
            
            
        }
        
        $provider = new \yii\data\ArrayDataProvider([
                            'allModels' => isset($data)?$data:[],
                            'pagination' => [
                                'pageSize' => 15,
                            ],
                        ]);
        
        return $this->renderAjax('_view_table', [
                    'provider' => $provider,
        ]);
    }
    
    public function actionViewTableWide($id) {
        $table = 'zdata_view_table';
        $data = [];
        $dataConfig = Yii::$app->db->createCommand("SELECT * FROM $table WHERE id=:id", [':id'=>$id])->queryOne();
        if($dataConfig){
            $ezf_id = $dataConfig['cform'];
            $left_fields = SDUtility::string2Array($dataConfig['set_left']);
            $fields = SDUtility::string2Array($dataConfig['cfields']);
            $sort_by = SDUtility::string2Array($dataConfig['sort_by']);
                    
            $field_target = EzfQuery::getTargetOne($ezf_id);
            $ezf_id_main = $ezf_id;
            if($field_target){
                if(isset($field_target['parent_ezf_id']) && !empty($field_target['parent_ezf_id'])){
                    $ezf_id_main = $field_target['parent_ezf_id'];
                }
            }

            $ezform = EzfQuery::getEzformById($ezf_id);
            $ezformParent = EzfQuery::getEzformById($ezf_id_main);
                
            $query = new \yii\db\Query();
            $query->from($ezform['ezf_table']);
            $query->where("{$ezform['ezf_table']}.rstat not in(0,3)");
                    
            $select = [];
            $alias_select = [];
            $ezformTmpUnipue = [];
            $ezformTmp[$ezform['ezf_id']] = $ezform['ezf_table'];
            if (isset($field_target) && $ezf_id!=$ezf_id_main) {// join 
                $ezformTmp[$ezformParent['ezf_id']] = $ezformParent['ezf_table'];
                $pkjoin = $field_target['ezf_field_name'];
                if(isset($field_target['ref_form']) && !empty($field_target['ref_form'])){
                    $ref_form = SDUtility::string2Array($field_target['ref_form']);
                    $pkjoin = $ref_form[$ezf_id_main];
                }

                $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`id` = `{$ezform['ezf_table']}`.`{$pkjoin}`");
                $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
                $query->groupBy("{$ezformParent['ezf_table']}.id");
            }
            
            foreach ($left_fields as $key => $field) {
                $obj_item = explode(':', $field['select']);
                $form = isset($obj_item[0])?$obj_item[0]:0;
                $table = isset($obj_item[1])?$obj_item[1]:'';
                $field_name = isset($obj_item[2])?$obj_item[2]:'';

                //select
                if(isset($alias_select[$field_name])){
                   $select[] = "`$table`.`$field_name` AS {$field_name}_{$alias_select[$field_name]}";  
                } else {
                    $select[] = "`$table`.`$field_name`";
                }

                $alias_select[$field_name] = isset($alias_select[$field_name])?$alias_select[$field_name]+1:2;
            }
            
            foreach ($fields as $key => $field) {
                $obj_item = explode(':', $field['select']);
                $form = isset($obj_item[0])?$obj_item[0]:0;
                $table = isset($obj_item[1])?$obj_item[1]:'';
                $field_name = isset($obj_item[2])?$obj_item[2]:'';

                //select
                if(isset($alias_select[$field_name])){
                   $select[] = "`$table`.`$field_name` AS {$field_name}_{$alias_select[$field_name]}";  
                } else {
                    $select[] = "`$table`.`$field_name`";
                }

                $alias_select[$field_name] = isset($alias_select[$field_name])?$alias_select[$field_name]+1:2;

                //join
                if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $form) {

                } else {
                    if (!in_array($form, $ezformTmpUnipue)) {


                        $modelTarget = EzfQuery::getTargetOne($form);
                        $refFormCond = SDUtility::string2Array($modelTarget['ref_form']);
                        $refFormTarget = [];
                        if (isset($targetField)) {
                            $refFormTarget = SDUtility::string2Array($targetField['ref_form']);
                        }

                        $lvlTarget = count($refFormTarget);
                        $lvlCond = count($refFormCond);


                        if ($lvlTarget < $lvlCond) {//ต่ำกว่าฟอร์มตั้งต้น
                            if (isset($refFormCond[$ezform['ezf_id']])) {
                                $joinField = $refFormCond[$ezform['ezf_id']];
                                $modelEzfCond = EzfQuery::getFormTableName($form);
                                $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                            } elseif ($modelTarget['ref_ezf_id'] == $ezform['ezf_id']) {// กรณี ref กัน 1 lvl
                                $joinField = $modelTarget['ezf_field_name'];
                                $modelEzfCond = EzfQuery::getFormTableName($form);
                                $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                            }
                        } elseif ($lvlTarget == $lvlCond) {
                            $modelEzfCond = EzfQuery::getFormTableName($form);
                            if (isset($modelTarget)) {//ต่ำกว่าฟอร์มตั้งต้น
                                if ($ezform['ezf_id'] == $modelTarget['ref_ezf_id']) {
                                    $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.{$modelTarget['ezf_field_name']} = {$ezform['ezf_table']}.id AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");
                                    $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                }
                            } else {//สูงกว่าฟอร์มตั้งต้น
                                if (isset($targetField)) {
                                    if ($form == $targetField['ref_ezf_id']) {
                                        $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.{$targetField['ezf_field_name']} AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");
                                        $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                    }
                                }
                            }
                        } elseif ($lvlTarget > $lvlCond) {//สูงกว่าฟอร์มตั้งต้น
                            if (isset($refFormTarget[$form])) {
                                $joinField = $refFormTarget[$form];
                                $modelEzfCond = EzfQuery::getFormTableName($form);
                                $query->leftJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.$joinField AND {$modelEzfCond['ezf_table']}.rstat not in(0,3)");

                                $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                            }
                        }

                        $ezformTmpUnipue[] = $form;
                    }
                }

            }
            
            $sort_array = [];
            foreach ($sort_by as $key => $field) {
                $obj_item = explode(':', $field['select']);
                $form = isset($obj_item[0])?$obj_item[0]:0;
                $table = isset($obj_item[1])?$obj_item[1]:'';
                $field_name = isset($obj_item[2])?$obj_item[2]:'';
                
                if($table!='' && $field_name!=''){
                    $sort_array["`$table`.`$field_name`"] = SORT_ASC;//SORT_ASC
                }
            }
            
            
            $query->select($select)->orderBy($sort_array);
            
            $data = [];
            if(isset($dataConfig['cformt']) && $dataConfig['cformt']==2)  {//Wide
                $tmp_name = 'tmp_ezform_'. SDUtility::getMillisecTime();
                $sql_tmp = "CREATE TEMPORARY TABLE {$tmp_name} ".$query->createCommand()->rawSql;
                
                $tmp_ex = Yii::$app->db->createCommand($sql_tmp)->execute();
                if($tmp_ex){
                    //send to program R (Long=>Wide)
                    
                    
                    $sql_get_tmp = "SELECT * FROM {$tmp_name}";
                    $data = Yii::$app->db->createCommand($sql_get_tmp)->queryAll();
                }
            } else {//Long
                $data = $query->createCommand()->queryAll();
            }   
            
            
        }
        
        $provider = new \yii\data\ArrayDataProvider([
                            'allModels' => isset($data)?$data:[],
                            'pagination' => [
                                'pageSize' => 15,
                            ],
                        ]);
        
        return $this->renderAjax('_view_table', [
                    'provider' => $provider,
        ]);
    }
    
    public function actionInputHelp() {
        $lang = \backend\modules\ezforms2\classes\EzfFunc::getLanguage();
                
        $model = \backend\modules\ezforms2\models\EzformInput::find()
                ->select(['ezform_input.*', 'c.content AS content'])
                ->where('input_active=1 AND ( ISNULL(input_category) OR input_category>0 )')
                ->leftJoin('content_lang c', 'c.obj_id = input_id AND title = :title AND language = :language', [':title'=>'ezinput', ':language'=>$lang])
                ->orderBy('input_order');
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model,
            'pagination' => false,
        ]);
        
        return $this->render('input_help', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Ezform model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (Yii::$app->getRequest()->isAjax) {
            $userProfile = Yii::$app->user->identity->profile;
            $auto = isset($_GET['auto'])?$_GET['auto']:0;
            
            $model = new Ezform();
            $model->ezf_id = SDUtility::getMillisecTime();
            $model->ezf_name = Yii::t('ezform', 'Unnamed Form');
            $model->ezf_version = 'v1';
            $model->status = 1;
            $model->xsourcex = $userProfile->sitecode;
            $model->public_listview = 0;
            $model->public_delete = 0;
            $model->public_edit = 0;
            $model->consult_tools = 1;
            $model->unique_record = 1;
            $model->query_tools = 1;
            $model->shared = 0;
            
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->xsourcex = $userProfile->sitecode;
                
                $result = EzfForm::saveEzfForm($model);
                return $result;
            } else {
                return $this->renderAjax('create', $this->dataRender($model, $auto));
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionAdd() {
        if (Yii::$app->getRequest()->isAjax) {
            
            \backend\modules\manageproject\classes\CNFunc::addLog('View Create Ezform');
            
            $searchModel = new EzformSearch();
            $searchModel->category_id = 4;
            $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, 8, 1);
            $dataProvider->pagination = [
                'pageSize' => 18,
            ];
            
            $sum = [];
            if (isset($_FILES['excel_file']['name']) && $_FILES['excel_file']['name'] != '') {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                ini_set('max_execution_time', 0);
                set_time_limit(0);
                ini_set('memory_limit', '256M');

                $excel_file = UploadedFile::getInstanceByName('excel_file');

                if ($excel_file) {

                    $sum = \backend\modules\ezforms2\classes\EzfFunc::importForm($excel_file->tempName, 1);
                    if(isset($sum['Ezform']['ezf_id'])){
                        $modelNew = Ezform::findOne($sum['Ezform']['ezf_id']);

                        $modelFav = new \backend\modules\ezforms2\models\EzformFavorite();
                        $modelFav->ezf_id = $modelNew->ezf_id;
                        $modelFav->userid = Yii::$app->user->id;
                        $modelFav->forder = EzfQuery::getOrderFav(Yii::$app->user->id);
                        $modelFav->save();
                    }
                    $html = $this->renderAjax('_sum_import', ['sum'=>$sum]);
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                        'html' => $html,
                        'id' => isset($sum['Ezform']['ezf_id'])?$sum['Ezform']['ezf_id']:0
                    ];
                    return $result;

                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('yii', 'File upload failed.'),
                        'html' => $html,
                    ];
                    return $result;
                }
            } else {
                return $this->renderAjax('_form_create', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionSave() {
        if (Yii::$app->getRequest()->isAjax) {
            
            $id = isset($_GET['id'])?$_GET['id']:0;
            $auto = isset($_GET['auto'])?$_GET['auto']:0;

            $userProfile = Yii::$app->user->identity->profile;
            $model = Ezform::findOne($id);
            
            if($model){
                EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'update');
                $model->ezf_options = SDUtility::string2Array($model->ezf_options);
                
            } else {
                $model = new Ezform();
                $model->ezf_id = SDUtility::getMillisecTime();
                $model->ezf_name = 'Unnamed Form';
                $model->ezf_version = 'v1';
                $model->status = 1;
                $model->xsourcex = $userProfile->sitecode;
                $model->public_listview = 0;
                $model->public_delete = 0;
                $model->public_edit = 0;
                $model->consult_tools = 1;
                $model->unique_record = 1;
                $model->query_tools = 1;
                $model->shared = 0;
            }
            $version = $model->ezf_version;
            
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->xsourcex = $userProfile->sitecode;
                
                if($version != $model->ezf_version){
                    $modelCurren = $this->findModel($id);

                    $model->field_detail = SDUtility::string2Array($modelCurren->field_detail);
                    $model->ezf_sql = $modelCurren->ezf_sql;
                    $model->ezf_js = $modelCurren->ezf_js;
                    $model->ezf_error = $modelCurren->ezf_error;
                    $model->ezf_options = SDUtility::string2Array($modelCurren->ezf_options);

                }

                $options = Yii::$app->request->post('Options', []);
                if(!empty($options)){

                    $model->ezf_options = ArrayHelper::merge($model->ezf_options, $options);
                }

                $result = EzfForm::saveEzfForm($model);
                \backend\modules\manageproject\classes\CNFunc::addLog("Save form {$model->ezf_name} ".SDUtility::array2String($model));
                return $result;
            } else {
                return $this->renderAjax('_form_mini', $this->dataRender($model, $auto));
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    /**
     * Updates an existing Ezform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $userProfile = Yii::$app->user->identity->profile;
        
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'update');
        $model->ezf_options = SDUtility::string2Array($model->ezf_options);
        $version = $model->ezf_version;
        
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->xsourcex = $userProfile->sitecode;
            if($version != $model->ezf_version){
                $modelCurren = $this->findModel($id);
                
                $model->field_detail = SDUtility::string2Array($modelCurren->field_detail);
                //$model->ezf_sql = $modelCurren->ezf_sql;
                $model->ezf_js = $modelCurren->ezf_js;
                $model->ezf_error = $modelCurren->ezf_error;
                $model->ezf_options = SDUtility::string2Array($modelCurren->ezf_options);
                $model->ezf_sql = SDUtility::string2Array($modelCurren->ezf_sql);
            }
            
            $options = Yii::$app->request->post('Options', []);
            if(isset($options['lock_data']) && $options['lock_data']==1){
                $options['lock_date'] = date('Y-m-d');
            } else {
                $options['lock_data'] = 0;
                $options['lock_date'] = NULL;
            }
            
            if(!empty($options)){
                $model->ezf_options = ArrayHelper::merge($model->ezf_options, $options);
            }
            $result = EzfForm::saveEzfForm($model);
            \backend\modules\manageproject\classes\CNFunc::addLog("Update form {$model->ezf_name} ". json_encode($result));
            return $result;
        } else {
            \backend\modules\manageproject\classes\CNFunc::addLog("View update form {$model->ezf_name}");
            return $this->renderAjax('update', $this->dataRender($model));
        }
    }
    
    public function actionLock($id, $status) {
        $model = $this->findModel($id);
        $options = Yii::$app->request->post('Options', []);
        
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'update');
        $model->ezf_options = SDUtility::string2Array($model->ezf_options);
        $options['lock_data'] = $status;
        $version = $model->ezf_version;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if(isset($options['lock_data']) && $options['lock_data']==1){
            $options['lock_date'] = date('Y-m-d');
        } else {
            $options['lock_date'] = NULL;
        }

        if(!empty($options)){
            $model->ezf_options = ArrayHelper::merge($model->ezf_options, $options);
        }

        $result = EzfForm::saveEzfForm($model);
        return $result;
    }
    
    public function actionManageToken($ezf_id) {
        $userProfile = Yii::$app->user->identity->profile;
        $reload = isset($_GET['reload'])?$_GET['reload']:'0';
        
        $model = $this->findModel($ezf_id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'update');
        $model->ezf_options = SDUtility::string2Array($model->ezf_options);
        $version = $model->ezf_version;
        
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->xsourcex = $userProfile->sitecode;
            if($version != $model->ezf_version){
                $modelCurren = $this->findModel($ezf_id);
                
                $model->field_detail = SDUtility::string2Array($modelCurren->field_detail);
                $model->ezf_sql = $modelCurren->ezf_sql;
                $model->ezf_js = $modelCurren->ezf_js;
                $model->ezf_error = $modelCurren->ezf_error;
                $model->ezf_options = SDUtility::string2Array($modelCurren->ezf_options);
                
            }
            
            $options = Yii::$app->request->post('Options', []);
            
            if(!empty($options)){
                $ezf_options = ArrayHelper::merge($model->ezf_options, $options);
                if($options['enable_token']==0){
                    unset($ezf_options['token']);
                }
                $model->ezf_options = $ezf_options;
            }
            $result = EzfForm::saveEzfForm($model);
            return $result;
        } else {
            return $this->renderAjax('_form_token', $this->dataRender($model, 0, $reload));
        }
    }

    private function dataRender($model, $auto=0, $reload=0) {
        $modelFields = EzformFields::find()
                ->select(['ezf_field_name', 'ezf_field_label'])
                ->where('ezf_id = :ezf_id', [':ezf_id' => $model->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return [
            'model' => $model, 'modelFields' => $modelFields , 'auto' => $auto, 'reload'=>$reload
        ];
    }

    /**
     * Deletes an existing Ezform model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionTrash($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'delete');
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = EzfForm::trashEzfForm($model);
            \backend\modules\manageproject\classes\CNFunc::addLog("Delete form {$model->ezf_name} ".SDUtility::array2String($model));
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionToken($ezf_id) {
        $model = $this->findModel($ezf_id);
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {
                $token = Yii::$app->getSecurity()->generateRandomString(20);
                $options = \appxq\sdii\utils\SDUtility::string2Array($model->ezf_options);
                
                $saveToken = 0;
                $options['enable_token'] = 1;
                $options['token'] = $token;
                $saveToken = 1;
                
                $model->ezf_options = \appxq\sdii\utils\SDUtility::array2String($options);
                
                if ($model->save()) {
                    if($saveToken){
                        try {
                            $modelToken = new \backend\modules\ezforms2\models\EzformToken();
                            $modelToken->token = $token;
                            $modelToken->ezf_id = $ezf_id;
                            $modelToken->user_id = Yii::$app->user->id;
                            $modelToken->save();
                            
                            $modelVersion = EzfQuery::getEzformConfig($model->ezf_id, $model->ezf_version);

                            $modelVersion->field_detail = $model->field_detail;
                            $modelVersion->ezf_sql = $model->ezf_sql;
                            $modelVersion->ezf_js = $model->ezf_js;
                            $modelVersion->ezf_error = $model->ezf_error;
                            $modelVersion->ezf_options = $model->ezf_options;
                            $modelVersion->save();
                        } catch (\yii\db\Exception $e) {
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                    
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model,
                    ];
                    \backend\modules\manageproject\classes\CNFunc::addLog("Create token {$model->ezf_name} ". SDUtility::array2String($model));
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            } catch (\yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                    'data' => $model,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'delete');
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = EzfForm::deleteEzfForm($model);
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDeletes() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset($_POST['selection'])) {
                foreach ($_POST['selection'] as $id) {
                    $model = $this->findModel($id);
                    $result = EzfForm::deleteEzfForm($model);
                    if ($result['status'] == 'error') {
                        break;
                    }
                }

                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                    'data' => $id,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionImport() {

        $sum = [];
        if (isset($_FILES['excel_file']['name']) && $_FILES['excel_file']['name'] != '') {
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $excel_file = UploadedFile::getInstanceByName('excel_file');
            
            if ($excel_file) {

                $sum = \backend\modules\ezforms2\classes\EzfFunc::importForm($excel_file->tempName, 0);

                Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                    'options' => ['class' => 'alert-success']
                ]);
            } else {
                Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgSuccess() . Yii::t('yii', 'File upload failed.'),
                    'options' => ['class' => 'alert-success']
                ]);
            }
        }

        return $this->render('import', [
                    'sum' => $sum,
        ]);
    }

    public function actionClone($ezf_id) {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $dataEzform = EzfQuery::getEzformById($ezf_id);
        
        $modelEzf = $this->findModel($ezf_id);
        $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $modelEzf->ezf_version);
        
        $filename = \backend\modules\ezforms2\classes\EzfFunc::exportForm($ezf_id, $modelEzf, $modelVersion);
        if (isset($filename) && !empty($filename)) {
            $sum = \backend\modules\ezforms2\classes\EzfFunc::importForm(Yii::getAlias('@backend/web/print/') . $filename, 1);
            
            if(isset($sum['Ezform']['ezf_id'])){
                $modelNew = Ezform::findOne($sum['Ezform']['ezf_id']);
                
                $modelFav = new \backend\modules\ezforms2\models\EzformFavorite();
                $modelFav->ezf_id = $modelNew->ezf_id;
                $modelFav->userid = Yii::$app->user->id;
                $modelFav->forder = EzfQuery::getOrderFav(Yii::$app->user->id);
                $modelFav->save();
            }
        }

        Yii::$app->session->setFlash('alert', [
            'body' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
            'options' => ['class' => 'alert-success']
        ]);

        return $this->redirect('/ezforms2/ezform/index');
    }
    
    public function actionCloneAjax($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');
            
            $db = Yii::$app->db;
            $dynamic = 0;
//            if(\appxq\sdii\utils\SDUtility::checkInternetConnection()){
//                $db = Yii::$app->db_main;
//                $dynamic = 1;
//            }
            
            $modelEzf = Ezform::find()->where('ezf_id=:id', [':id'=>$ezf_id])->one($db);
            
            $ezfName = isset($modelEzf['ezf_name']) ? $modelEzf['ezf_name'] : '';
            \backend\modules\manageproject\classes\CNFunc::addLog("Clone form {$ezfName} ".SDUtility::array2String($modelEzf));
            
            $modelVersion = \backend\modules\ezforms2\models\EzformVersion::find()
                ->where("ezf_id = :ezf_id AND ver_code = :ezf_version", [':ezf_id' => $modelEzf->ezf_id, ':ezf_version' => $modelEzf->ezf_version])
                ->one($db);

            $filename = \backend\modules\ezforms2\classes\EzfFunc::exportForm($ezf_id, $modelEzf, $modelVersion, $dynamic, $db );
            if (isset($filename) && !empty($filename)) {
                $sum = \backend\modules\ezforms2\classes\EzfFunc::importForm(Yii::getAlias('@backend/web/print/') . $filename, 1);
                
                if(isset($sum['Ezform']['ezf_id'])){
                    $modelNew = Ezform::findOne($sum['Ezform']['ezf_id']);
                    
                    $modelFav = new \backend\modules\ezforms2\models\EzformFavorite();
                    $modelFav->ezf_id = $modelNew->ezf_id;
                    $modelFav->userid = Yii::$app->user->id;
                    $modelFav->forder = EzfQuery::getOrderFav(Yii::$app->user->id);
                    $modelFav->save();
                }
                
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                    'id' => isset($sum['Ezform']['ezf_id'])?$sum['Ezform']['ezf_id']:0
                ];
                return $result;
            } 

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('yii', 'File upload failed.'),
            ];
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }    
    }

    public function actionGetFields($q = null, $id = null) {
        $ezf_id = Yii::$app->request->get('ezf_id', 0);
        $v = Yii::$app->request->get('v', '');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        
        $sqladdon = ' AND (ezf_version = :v OR ezf_version="all") ';
        $params = [':q' => "%$q%", ':id' => $ezf_id];
        if($v!=''){
            $params[':v'] = $v;
        }
        
        $sql = "SELECT ezf_field_name AS `id`,  IF(ezf_field_label<>'' OR ezf_field_label<>Null,ezf_field_label,ezf_field_name) AS`name` , ezf_version FROM `ezform_fields` WHERE `ezf_id` = :id AND table_field_type <> 'none' AND CONCAT(`ezf_field_name`, `ezf_field_label`) LIKE :q $sqladdon ORDER BY ezf_version, ezf_field_order LIMIT 0,50";

        $data = Yii::$app->db->createCommand($sql, $params)->queryAll();
        $i = 0;

        foreach ($data as $value) {
            $out["results"][$i] = ['id' => "{$value['id']}", 'text' => $value["name"]." [{$value['ezf_version']}]"];
            $i++;
        }

        return $out;
    }

    public function actionGetForms($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        
        $query = new \yii\db\Query;
        $query->select('ezf_id AS id, ezf_name AS text')
            ->from('ezform')
            ->where('ezf_name like :q AND status = 1 ', [':q'=>"%$q%"])
            ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])   
            ->orderBy('created_at DESC')    
            ->limit(50);
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out['results'] = array_values($data);
        
        return $out;
    }
    
    public function actionGetFormsGroup($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = isset($_GET['category_id'])?$_GET['category_id']:'';
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        
        $query = new \yii\db\Query;
        $query->select('ezf_id AS id, ezf_name AS text')
            ->from('ezform')
            ->where('ezf_name like :q AND status = 1 ', [':q'=>"%$q%"])
            ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])   
            ->orderBy('created_at DESC')    
            ->limit(50);
        
        if(!empty($category_id)){
            $query->andWhere('category_id=:category_id', [':category_id'=>$category_id]);
        }
        
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out['results'] = array_values($data);
        
        return $out;
    }
    
    public function actionGetFavoriteForms($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        
        $query = new \yii\db\Query;
        $query->select('ezform.ezf_id AS id, ezform.ezf_name AS text')
            ->from('ezform')
            ->innerJoin('ezform_favorite', 'ezform_favorite.ezf_id = ezform.ezf_id')
            ->where('ezf_name like :q AND status = 1 AND ezform_favorite.userid = :userid', [':q'=>"%$q%", ':userid'=>Yii::$app->user->id])
            ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])      
            ->orderBy('created_at DESC')    
            ->limit(50);
        
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out['results'] = array_values($data);
        
        return $out;
    }
    
    public function actionGetEzunitForms($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dept = isset($_GET['dept'])?$_GET['dept']:'';
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        
        $query = new \yii\db\Query;
        $query->select([
            '`in_ezf_id` AS id',
            "`tab_name` AS text",
        ])
            ->from('zdata_working_unit_setting')
            ->innerJoin('zdata_working_unit u', 'u.id = zdata_working_unit_setting.unit_target')
            //->innerJoin('ezform', 'ezform.ezf_id = zdata_working_unit_setting.in_ezf_id')
            ->where('(tab_name like :q) AND zdata_working_unit_setting.unit_code=:unit', [':q'=>"%$q%", ':unit'=>$dept])
            //->andWhere('ezform.shared = 1 OR (ezform.shared = 3 AND ezform.xsourcex=:xsourcex) OR (ezform.shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (ezform.shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => $userProfile->sitecode])    
            ->groupBy('in_ezf_id')
            ->limit(50);
        
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out['results'] = array_values($data);
        
        return $out;
    }
    
    public function actionGetRole($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $query = new \yii\db\Query;
        $query->select(['zdata_role_permissions.role_name AS id', "zdata_role_permissions.role_desc AS text"])
            ->from('zdata_role_permissions')
            ->where('zdata_role_permissions.role_name like :q OR zdata_role_permissions.role_desc like :q', [':q'=>"%$q%"])
            ->andWhere('rstat not in(0,3) ') //AND sitecode = :sitecode , [':sitecode'=>$sitecode]
            ->limit(50);
        $command = $query->createCommand();
        $data = $command->queryAll();
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value["text"]];
        }
        
        return $out;
    }
    
    public function actionGetUser($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $query = new \yii\db\Query;
        $query->select(['profile.user_id AS id', "Concat(profile.firstname, ' ', profile.lastname) AS text"])
            ->from('profile')
            ->innerJoin('user', 'user.id = profile.user_id')    
            ->where(['like',"Concat(profile.firstname, ' ', profile.lastname)", $q])
            ->andWhere('user.confirmed_at is not null AND user.blocked_at is null AND sitecode = :sitecode', [':sitecode'=>$sitecode])
            ->orderBy('text')    
            ->limit(50);
        $command = $query->createCommand();
        $data = $command->queryAll();
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value["text"]];
        }
        
        return $out;
    }
    
    public function actionGetUserMulti($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        
        if (is_null($q)) {
            $q = '';
        }
        
        $query = new \yii\db\Query;
        $query->select(['profile.user_id AS id', "Concat(profile.firstname, ' ', profile.lastname) AS text"])
            ->from('profile')
            ->innerJoin('user', 'user.id = profile.user_id')    
            ->where(['like',"Concat(profile.firstname, ' ', profile.lastname)", $q])
            ->andWhere('user.confirmed_at is not null AND user.blocked_at is null AND sitecode = :sitecode', [':sitecode'=>$sitecode])
            ->orderBy('text')    
            ->limit(50);
        $command = $query->createCommand();
        $data = $command->queryAll();
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value["text"]];
        }

        return $out;
    }
    
    public function actionFormOauthe() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
            $field = isset($_GET['field'])?$_GET['field']:0;
            $dataid = isset($_GET['dataid'])?$_GET['dataid']:'';
            $v = isset($_GET['v'])?$_GET['v']:0;
            $action = isset($_GET['action'])?\backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($_GET['action']):[];
            $reloadDiv = isset($_GET['reloadDiv'])?$_GET['reloadDiv']:'';
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $model_field = EzfQuery::getFieldById($field);
            $share_options = SDUtility::string2Array($model_field['share_options']);
            $html = '';
            

            if (isset($_POST['ezpw'])) {
                try {
                    if(isset($_POST['ezpw_save']) && $_POST['ezpw_save']==1){
                        Yii::$app->session["ezpw_{$ezf_id}_{$field}"] = $_POST['ezpw'];
                    } else {
                        unset(Yii::$app->session["ezpw_{$ezf_id}_{$field}"]);
                    }
                    
                    if (isset($share_options['pw']) && $share_options['pw']==$_POST['ezpw']) {
                        
                        $html = $this->renderAjax('_finput', [
                            'ezf_id' => $ezf_id,
                            'field' => $field,
                            'dataid' => $dataid,
                            'action' => $action,
                            'v' => $v,
                            'model_field' => $model_field,
                        ]);
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'html' => $html,
                        ];
                        return $result;
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid password.'),
                            'html' => $html,
                        ];
                        return $result;
                    }
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid password.'),
                    ];
                    return $result;
                }
            } else {
                $html = $this->renderAjax('_form_oauthe', [
		    'ezf_id' => $ezf_id,
                    'field' => $field,
                    'dataid' => $dataid,
                    'action' => $action,
                    'v' => $v,
                    'model_field' => $model_field,
                    'reloadDiv' => $reloadDiv,
		]);
                
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                    'html' => $html,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    /**
     * Finds the Ezform model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Ezform the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ezform::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetDepdrop($ezf_id, $ezf_field_id){
	$out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
	if (isset($_POST['depdrop_parents'])) {
            $target = EzfQuery::getTargetOneEzform($ezf_id);
            $field = EzfQuery::getFieldById($ezf_field_id);
            $modelFields = EzfQuery::findSpecialOne($ezf_id);
            $options = SDUtility::string2Array($field['ezf_field_options']);
            $ezform = EzfQuery::getEzformOne($field['ref_ezf_id']);
            
            $dependField = isset($options['options']['data-depend'])?$options['options']['data-depend']:'';
            if($dependField==''){
                return ['output'=>'', 'selected'=>''];
            }
            
            if(isset($target)){
                $table = $target['ezf_table'];
                $ref_id = $target['ref_field_id'];
                $nameConcat = \backend\modules\ezforms2\classes\EzfFunc::array2ConcatStr($field['ref_field_desc']);
                if (!$nameConcat) {
                    return ['output'=>'', 'selected'=>''];
                }
                
                $parents = $_POST['depdrop_parents'];
                $ids = $_POST['depdrop_parents'];
                $id = empty($ids[0]) ? null : $ids[0];
                if ($id != null) {
                    $param1 = null;
                    if (!empty($_POST['depdrop_params'])) {
                        $params = $_POST['depdrop_params'];
                        $param1 = $params[0]; // get the value of input-type-1
                    }
                    
                    $dependModel = EzfQuery::getFieldDependOne($field['ezf_id'], $dependField);
                    if(isset($dependModel) && $dependModel['ref_field_id'] != 'id'){
                        $subModel = EzfQuery::getTargetOneEzform($field['ref_ezf_id']);
                        $parentData = EzfQuery::getEzformById($subModel['ref_ezf_id']);

                        $queryp = new \yii\db\Query();
                        $queryp->select(["id"]);
                        $queryp->from("`{$parentData['ezf_table']}`");
                        $queryp->where("`{$dependModel['ref_field_id']}` = :id AND rstat not in(0, 3) ", [':id'=>$id]);

                        $dataParent = $queryp->createCommand()->queryScalar();
                        if($dataParent){
                            $id = $dataParent;
                        }
                    }

                    $query = new \yii\db\Query();
                    $query->select(["`$ref_id` AS id", "$nameConcat AS `name`"]);
                    $query->from("`$table`");
                    $query->where("`{$target['ezf_field_name']}` = :id  AND rstat not in(0, 3)", [':id' => $id]);
                    $query->limit(50);
                    
                    if (isset($modelFields) || $ezform['public_listview'] == 2) {
                        $query->andWhere('xsourcex = :site', [':site'=>Yii::$app->user->identity->profile->sitecode]);
                     }

                     if ($ezform['public_listview'] == 3) {
                         $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
                     }

                     if ($ezform['public_listview'] == 0) {
                         $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
                     }

                     if(isset($modelFieldsTarget) && $target!=''){
                        $query->andWhere("{$modelFieldsTarget['ezf_field_name']} = :target", [':target'=>$target]);
                     }

                    $data = $query->createCommand()->queryAll();
                    
                    $out = array_values($data);
                    return ['output'=>empty($out)?'':$out, 'selected'=>$param1];
                }
            }
	    
	}
         
	return ['output'=>'', 'selected'=>''];
    }
    
    public function actionMultiSave() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
            $save_ezf_id = isset($_GET['save_ezf_id'])?$_GET['save_ezf_id']:0;
            $id = isset($_POST['id'])?$_POST['id']:0; //where target
            $target = isset($_GET['target'])?$_GET['target']:'';
            $unset_fields = isset($_GET['unset_fields'])?$_GET['unset_fields']:'';
            $unset_fields = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($unset_fields);
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $modelEzf = EzfQuery::getEzformOne($save_ezf_id);
            $ezform_tmp = EzfQuery::getEzformById($ezf_id);
            if($ezform_tmp && $modelEzf){
                $table = $ezform_tmp['ezf_table'];
                $version = $modelEzf->ezf_version;
                
                $sql = "SELECT * FROM `$table` WHERE rstat not in(0,3) AND target = :target";
                $data_tmp = Yii::$app->db->createCommand($sql, [':target'=>$id])->queryAll();
                if($data_tmp){
                    Yii::$app->session['show_varname'] = 0;
                    Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

                    $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
                    $evenFields = \backend\modules\ezforms2\classes\EzfFunc::getEvenField($modelFields);
                    $systemFields = [];
                    if (isset($evenFields['target']) && !empty($evenFields['target'])) {
                        $modelTarget = $evenFields['target'];
                        if (isset($modelTarget['ref_form']) && !empty($modelTarget['ref_form'])) {
                            $refForm = \appxq\sdii\utils\SDUtility::string2Array($modelTarget['ref_form']);
                            $systemFields = array_values($refForm);
                        }
                    }
                    //\appxq\sdii\utils\VarDumper::dump($modelFields,1,0);
                    foreach ($data_tmp as $key => $value) {
                        unset($value['id']);
                        unset($value['ptid']);
                        unset($value['xsourcex']);
                        unset($value['xdepartmentx']);
                        unset($value['rstat']);
                        unset($value['sitecode']);
                        unset($value['ptcode']);
                        unset($value['ptcodefull']);
                        unset($value['hptcode']);
                        unset($value['hsitecode']);
                        unset($value['user_create']);
                        unset($value['create_date']);
                        unset($value['user_update']);
                        unset($value['update_date']);
                        unset($value['target']);
                        unset($value['sys_lat']);
                        unset($value['sys_lng']);
                        unset($value['ezf_version']);
                        
                        foreach ($systemFields as $key_sf => $value_sf) {
                            unset($value[$value_sf]);
                        }
                        
                        foreach ($unset_fields as $key_uf => $value_uf) {
                            unset($value[$value_uf]);
                        }
                        
                        //$value['ezf_version'] = $version;
                        $r = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsertEzform($modelEzf, $modelFields, '', $target, $value);
                    }
                }
            }
            
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                ];
            
                return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
}
