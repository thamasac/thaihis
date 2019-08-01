<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;


/**
 * Select2Controller implements the CRUD actions for EzformInput model.
 */
class Select2Controller extends Controller
{
    
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
	    
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/select2/_formitem', [
		'row' => $row,
	    ]);
	    
	    $result = [
		'status' => 'success',
		'action' => 'create',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'html' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionHospital($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `name` LIKE :q OR `code` LIKE :q ORDER BY `name` LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;

            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }

    public function actionGetSite($q = null, $id = null) {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $sql = "SELECT site_code AS id, CONCAT(site_code,' : ', site_name) AS text FROM zdata_sitecode WHERE CONCAT(site_code,' : ', site_name) LIKE :q AND rstat not in (0,3) ORDER BY `text` LIMIT 0,50";
        $data = Yii::$app->db->createCommand($sql, [":q" => "%$q%"])->queryAll();

        if ($data) {
            $out['results'] = array_values($data);
        } 
//        else {
//            $out['results'] = [['id'=>'-9999','text' => Yii::t('app', 'New')." '$q'"]];
//        }

        return $out;
    }
    
    public static function initSite($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT `site_code` AS `code`, CONCAT(site_code,' : ', site_name) AS text FROM `zdata_sitecode` WHERE `site_code`=:code AND rstat not in (0,3)";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['text'];
        }
        
        return $str;
    }
    
    public function actionSnomed($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q=urlencode($q);
        $json = file_get_contents("http://www.cascap.in.th:9201/snomed/description/_search?q=TERM:{$q}&size=50&sort=_score:desc");
        $arrayJson = json_decode($json,true);
        $data=$arrayJson['hits']['hits'];
        $i=0;
        foreach ($data as $snomed) {
                $json2 = file_get_contents("http://www.cascap.in.th:9201/snomed/concept/_search?q=".$snomed['_source']['CONCEPTID']);
                $arrayJson2 = json_decode($json2,true);
                $data2=$arrayJson2['hits']['hits']['0']['_source'];
                //print_r($data2);exit;
                $out['results'][$i] = ['id' => $snomed['_source']['DESCRIPTIONID'], 'text' => "<b>" . $snomed['_source']['TERM']."</b> (".$data2['FULLYSPECIFIEDNAME'].")"];
                $i++;
        }
        return  $out;
    }
    

    public function actionIcd10($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT * FROM `const_icd10` WHERE CONCAT(`code`, `name`) LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public function actionStamper($q = null, $id = null)
    {
        $auto_id = isset($_GET['auto_id']) ? $_GET['auto_id'] : 0;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $auto = \backend\modules\ezforms2\models\EzformAutonum::findOne($auto_id);
        $param = [':q'=>"%$q%", ':auto_id'=>$auto_id];
        $wstr = '';
        if($auto){
            if($auto->bysite==1){
                $wstr = ' AND xsourcex=:site ';
                $param[':site'] = Yii::$app->user->identity->profile->sitecode;
            }
        } else {
            return $out;
        }
        
        //Yii::$app->user->identity->profile->sitecode
                
            $sql = "SELECT * FROM `ezform_stamper` WHERE auto_id=:auto_id $wstr AND `auto_num` LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, $param)->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['auto_num'],'text'=>$value["auto_num"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public function actionIcd9($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT * FROM `const_icd9` WHERE CONCAT(`code`, `name`) LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public function actionGetInitSelect($ezf_id, $ezf_field_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $dataid = isset($_POST['dataid'])?$_POST['dataid']:0;
        $out = [
            'id'=>'',
            'text'=>'',
        ];
        
        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        if ($dataEzf) {
            $modelFieldsTarget = EzfQuery::getTargetOne($dataEzf['ref_ezf_id']);
            
            $table = $dataEzf['ezf_table'];
            $ref_id = $dataEzf['ref_field_id'];
            $nameConcat = EzfFunc::array2ConcatStr($dataEzf['ref_field_desc']);
            if (!$nameConcat) {
                return $out;
            }

            $searchConcat = EzfFunc::array2ConcatStr($dataEzf["ref_field_search"]);
            if (!$searchConcat) {
                return $out;
            }
        } else {
            return $out;
        }
        
        try {
            $sql = "SELECT `$ref_id` AS id, $nameConcat AS text FROM `$table` WHERE `$ref_id` = :id";
            $data = Yii::$app->db->createCommand($sql, [':id'=>$dataid])->queryOne();
            if($data){
                return [
                    'id'=>$data['id'],
                    'text'=>$data['text'],
                ];
            }
        } catch (\yii\db\Exception $e) {
            
        }
        
        return $out;
    }
    
    public static function initHospital($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initSnomed($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initIcd10($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT * FROM `const_icd10` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initIcd9($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT * FROM `const_icd9` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public function actionFindSql($q = null, $id = null) {
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $dataEzf = EzfQuery::getEzformOne($ezf_id);
        
        $query = new \yii\db\Query();
        $query->select(["`id` AS id", "sql_name AS `name`"]);
        $query->from("`zdata_ezsql`");
        $query->where("(sql_name LIKE :q OR sql_tags LIKE :q) AND sql_success = 1  AND rstat not in(0, 3)", [':q' => "%$q%"]);
        $query->orderBy('`name`');
        $query->limit(50);
        
        if(isset($dataEzf['public_listview']) && $dataEzf['public_listview']==0) {
            $query->andWhere('user_create = :user_id', [':user_id'=>Yii::$app->user->identity->profile->user_id]);
        }
        
        if(isset($dataEzf['public_listview']) && $dataEzf['public_listview']==2) {
            $query->andWhere('xsourcex = :xsourcex', [':xsourcex'=>Yii::$app->user->identity->profile->sitecode]);
        }
        
        if(isset($dataEzf['public_listview']) && $dataEzf['public_listview']==3) {
            $query->andWhere('xdepartmentx = :xdepartmentx', [':xdepartmentx'=>Yii::$app->user->identity->profile->department]);
        }
        
        $data = $query->createCommand()->queryAll();
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value["name"]];
        }

        return $out;
    }
    
    public function actionFindComponent($q = null, $id = null) {
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $initdata = isset($_GET['initdata']) ? $_GET['initdata'] : 0;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];

        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        //$modelFields = EzfQuery::findSpecialOne($ezf_id);
        $modelFieldsTarget = NULL;
        $ezform = EzfQuery::getEzformOne($dataEzf['ref_ezf_id']);
        
        if ($dataEzf) {
            $modelFieldsTarget = EzfQuery::getTargetOne($dataEzf['ref_ezf_id']);
            
            $table = $dataEzf['ezf_table'];
            $ref_id = $dataEzf['ref_field_id'];
            $nameConcat = EzfFunc::array2ConcatStr($dataEzf['ref_field_desc']);
            if (!$nameConcat) {
                return $out;
            }

            $searchConcat = EzfFunc::array2ConcatStr($dataEzf["ref_field_search"]);
            if (!$searchConcat) {
                return $out;
            }
        } else {
            return $out;
        }

        if (is_null($q)) {
            $q = '';
        }
        
        $select_attr = ["`$ref_id` AS xidx", "$nameConcat AS `xnamex`"];
        if($initdata==1){
            $fields = EzfQuery::getFieldsListAllVersion($dataEzf['ref_ezf_id']);
            if($fields){
                $fields_attr = \yii\helpers\ArrayHelper::getColumn($fields, 'ezf_field_name');
                unset($fields_attr['id']);
                unset($fields_attr[$dataEzf['ezf_field_name']]);
                
                $fields_attr = array_values($fields_attr);
                
                $select_attr = \yii\helpers\ArrayHelper::merge($select_attr, $fields_attr);
            }
            
        }
        
        
        $query = new \yii\db\Query();
        $query->select($select_attr);
        $query->from("`$table`");
        $query->where("$searchConcat LIKE :q  AND rstat not in(0, 3)", [':q' => "%$q%"]);
        $query->orderBy('`xnamex`');
        $query->limit(50);

        if ($ezform['public_listview'] == 2) {//isset($modelFields) || 
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
        
        if($initdata==1){
            foreach ($data as $value) {
                $results_data = ['id' => "{$value['xidx']}", 'text' => $value["xnamex"]];
                $results_obj = [];
                foreach ($value as $key_obj => $value_obj) {
                    if(!in_array($key_obj, ['xidx', 'xnamex', 'id', 'text'])){
                        $results_obj[$key_obj] = $value_obj;
                    }
                }
                
                $out["results"][] = \yii\helpers\ArrayHelper::merge($results_obj, $results_data);
            }
        } else {
            foreach ($data as $value) {
                $out["results"][] = ['id' => "{$value['xidx']}", 'text' => $value["xnamex"]];
            }
        }

        return $out;
    }

    public static function initComponent($model, $modelFields) {
        $options = SDUtility::string2Array($modelFields['ezf_field_options']);
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        
        $modelEzf = EzfQuery::getEzformOne($modelFields['ref_ezf_id']);

        $table = $modelEzf['ezf_table'];
        $ref_id = $modelFields['ref_field_id'];
        $nameConcat = EzfFunc::array2ConcatStr($modelFields['ref_field_desc']);
        
        if (!$nameConcat) {
            return $str;
        }
        
        if (isset($code) && !empty($code)) {
            if(isset($options['options']['multiple']) && $options['options']['multiple']==1){
                $sql = "SELECT `$ref_id` AS id, $nameConcat AS`name` FROM `$table`";
                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $str = $data;
            } else {
                $sql = "SELECT `$ref_id` AS id, $nameConcat AS`name` FROM `$table` WHERE `$ref_id` =:id";
                $data = Yii::$app->db->createCommand($sql, [':id' => $code])->queryOne();
                $str = $data['name'];
            }
        }

        return $str;
    }
    
    public static function actionCheckComp() {
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $btnreload = '';
        if($target == ''){
            if(isset($_POST['target'])){
                $target = $_POST['target'];
                $btnreload = Html::button('<i class="glyphicon glyphicon-refresh"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'Refresh'), 'class'=>'btn btn-default btn-reload']).' ';
            }
        }
        
        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        if($dataid!=''){
            $dataFields = EzfQuery::getRefFieldById($ezf_field_id);
            if($dataFields){
                if($dataFields['ref_field_id']!='id'){
                    
                    $newId = EzfQuery::builderSqlGetScalar(["id"], $dataFields['ezf_table'], "{$dataFields['ref_field_id']} = :dataid  AND rstat not in(0, 3)", [':dataid' => $dataid]);
                    if($newId){
                        $dataid = $newId;
                    }
                }
            }
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        $user_id = $userProfile->user_id;
        $created_by = 0;
        $codev = [];
        $assign = [];
        
        if ($dataEzf) {
            $created_by = $dataEzf['user_by'];
            $codev = SDUtility::string2Array($dataEzf['co_dev']);
            $assign = SDUtility::string2Array($dataEzf['assign']);
        } 
        
        $html = '';
        $html .= Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['class'=>'btn btn-default btn-cong', 'data-active'=>1, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$ezf_field_id, 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'target'=>$target, 'dataid'=>'']), 'data-id'=>$dataid]).' ';
        if($created_by == $user_id || $dataEzf['public_edit']==1 || in_array($user_id, $codev) || in_array($user_id, $assign)){
            if($dataid!=''){
                $html .= Html::button('<i class="glyphicon glyphicon-eye-open"></i> ', ['data-toggle'=>'tooltip', 'title'=> Yii::t('ezform', 'Open Form'), 'class'=>'btn btn-primary btn-open-ezform btn-edit', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'']), 'data-id'=>$dataid, 'style'=>$dataid>0?'':'display: none;']).' ';
            }
            $html .= Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'New'), 'class'=>'btn btn-success btn-open-ezform btn-add', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'target'=>$target ])]).' ';
            $html .= $btnreload;
        } else {
            $html .= Html::button(Yii::t('ezform', 'Form creator disabled.'), ['class'=>'btn btn-danger ']).' ';
        }
        
        return $html;
    }
    
    public static function actionCheckSqlbuilder() {
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $btnreload = '';
        if($target == ''){
            if(isset($_POST['target'])){
                $target = $_POST['target'];
                $btnreload = Html::button('<i class="glyphicon glyphicon-refresh"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'Refresh'), 'class'=>'btn btn-default btn-reload']).' ';
            }
        }
        
        $dataEzf = EzfQuery::getEzformOne($ezf_id);
        
        $userProfile = Yii::$app->user->identity->profile;
        $user_id = $userProfile->user_id;
        $created_by = 0;
        $codev = [];
        $assign = [];
        
        if ($dataEzf) {
            $created_by = $dataEzf['created_by'];
            $codev = SDUtility::string2Array($dataEzf['co_dev']);
            $assign = SDUtility::string2Array($dataEzf['assign']);
        } 

        $html = '';
        $html .= Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['class'=>'btn btn-default btn-cong', 'data-active'=>1, 'data-url'=>Url::to(['/ezforms2/select2/check-sqlbuilder', 'ezf_field_id'=>$ezf_field_id, 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'target'=>$target, 'dataid'=>'']), 'data-id'=>$dataid]).' ';
        if($created_by == $user_id || $dataEzf['public_edit']==1 || in_array($user_id, $codev) || in_array($user_id, $assign) || Yii::$app->user->can('administrator')){
            if($dataid!=''){
                $html .= Html::button('<i class="glyphicon glyphicon-eye-open"></i> ', ['data-toggle'=>'tooltip', 'title'=> Yii::t('ezform', 'Open Form'), 'class'=>'btn btn-primary btn-open-ezform btn-edit', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'']), 'data-id'=>$dataid, 'style'=>$dataid>0?'':'display: none;']).' ';
                $html .= Html::button('<i class="fa fa-files-o"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'Clone'), 'class'=>'btn btn-warning btn-open-ezform btn-clone', 'data-url'=>Url::to(['/ezforms2/select2/sql-clone', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'' ]), 'data-id'=>$dataid, 'style'=>$dataid>0?'':'display: none;']).' ';
            }
            $html .= Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'New'), 'class'=>'btn btn-success btn-open-ezform btn-add', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'target'=>$target ])]).' ';
            $html .= $btnreload;
        } else {
            if($dataid!=''){
                $html .= Html::button('<i class="fa fa-files-o"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'Clone'), 'class'=>'btn btn-warning btn-open-ezform btn-clone', 'data-url'=>Url::to(['/ezforms2/select2/sql-clone', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'' ]), 'data-id'=>$dataid, 'style'=>$dataid>0?'':'display: none;']).' ';
            }
            $html .= Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'New'), 'class'=>'btn btn-success btn-open-ezform btn-add', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'target'=>$target ])]).' ';
            $html .= $btnreload;
        }
        
        return $html;
    }
    
    public function actionHospitalDept($q = null, $sht = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT 
                        id,
                        unit_code,
                        unit_name,
                        unit_oipd_type
                    FROM zdata_working_unit zwu
                    WHERE zwu.rstat not in(0,3) AND unit_oipd_type LIKE :type AND (zwu.unit_code LIKE :q OR zwu.unit_name LIKE :q)
                LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%", ':type' => "$sht%"])->queryAll();
            $i = 0;

            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['id'],'text'=>$value["unit_code"]." : ".$value["unit_name"]];
                $i++;
            }
        
        return $out;
    }
    
    public static function initHospitalDept($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT id AS `code`, CONCAT(unit_code, ' : ', unit_name) AS `name` FROM zdata_working_unit zwu WHERE `id`=:id AND zwu.rstat not in(0,3)";
            $data = Yii::$app->db->createCommand($sql, [':id'=>$code])->queryOne();
            
            $str = isset($data['name']) ? $data['name'] : '';
        }
        
        return $str;
    }
    
    public function actionSearchUser($q = null, $item_name) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];
        
        $sql = "SELECT 
                    pro.user_id,
                    pro.certificate,
                    IFNULL(CONCAT(title, firstname, ' ', lastname), pro.`name`) AS fullname
            FROM profile pro 
            INNER JOIN auth_assignment aa ON pro.user_id=assign.user_id 
            WHERE assign.item_name=:item_name AND CONCAT(certificate, firstname, ' ', lastname) LIKE :q ORDER BY `fullname`";


        $data = Yii::$app->db->createCommand($sql, [':item_name' => $item_name, ':q' => "%$q%"])->queryAll();
        
        $i = 0;

        foreach ($data as $value) {
            $certi = isset($value['certificate']) ? $value['certificate'] : '';
            $result["results"][$i] = ['id' => $value['user_id'], 'text' => $certi . ' ' . $value["fullname"]];
            $i++;
        }

        return $result;
    }
    
    public static function initSelect2Doctor($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if (isset($code) && !empty($code)) {
            $sql = "SELECT 
                        pro.user_id,
                        pro.certificate,
                        IFNULL(CONCAT(title, firstname, ' ', lastname), pro.`name`) AS fullname
                    FROM profile pro 
                    INNER JOIN auth_assignment assign ON pro.user_id = assign.user_id 
                    WHERE pro.user_id = :id";

            $data = Yii::$app->db->createCommand($sql, [':id' => $code])->queryOne();
            
            $str = isset($data['fullname']) ? $data['fullname'] : '';
        }

        return $str;
    }
    
    public function actionSqlClone()
    {
        if (Yii::$app->getRequest()->isAjax) {
            $dataid = isset($_GET['dataid'])?$_GET['dataid']:'';
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:'';
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $table = $modelEzf['ezf_table'];
            $version = $modelEzf->ezf_version;
            
            try {
                $sql = "SELECT
                            *
                        FROM
                            `$table` 
                        WHERE
                        `rstat` not in(0,3) AND `id` = :id
                    ";
                $data = Yii::$app->db->createCommand($sql, [':id'=>$dataid])->queryOne();
                if($data){
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
                    
                    unset($data['id']);
                    unset($data['ptid']);
                    unset($data['xsourcex']);
                    unset($data['xdepartmentx']);
                    unset($data['rstat']);
                    unset($data['sitecode']);
                    unset($data['ptcode']);
                    unset($data['ptcodefull']);
                    unset($data['hptcode']);
                    unset($data['hsitecode']);
                    unset($data['user_create']);
                    unset($data['create_date']);
                    unset($data['user_update']);
                    unset($data['update_date']);
                    unset($data['target']);
                    unset($data['sys_lat']);
                    unset($data['sys_lng']);
                    unset($data['ezf_version']);

                    foreach ($systemFields as $key_sf => $value_sf) {
                        unset($data[$value_sf]);
                    }
                    
                    $data['sql_name'] = $data['sql_name'] . ' [Clone]';
                    $r = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsertEzform($modelEzf, $modelFields, '', '', $data);

                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('his', 'Clone completed.'),
                    ];
                    return $result;
                }
                
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgWarning() . Yii::t('his', 'Result not found.'),
                ];
                return $result;
            } catch (\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . $e->getMessage(),
                ];
                return $result;
            }
        } else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
}
