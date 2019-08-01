<?php

namespace backend\modules\study_manage\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\manage_modules\classes\ManageModuleFunc;
use yii\db\Exception;
use backend\modules\ezforms2\models\EzformSearch;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleSearch;
use backend\modules\study_manage\classes\StudyQuery;
use backend\modules\study_manage\models\StudyModuleSearch;

/**
 * Default controller for the `study_manage` module
 */
class UpdateToolController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {

        return $this->render('index', [
        ]);
    }

    public function actionEzforms() {

        $searchModel = new EzformSearch();
        $search_input = Yii::$app->request->get('search_input');
        $params = [];
        Yii::$app->request->queryParams;
        if ($search_input) {
            $params['ezf_name'] = $search_input;
        }
        $searchModel->ezf_name = $search_input;
        $dataProvider = $searchModel->search($params);

        return $this->renderAjax('_ezforms', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEzmodules() {
        $searchModel = new EzmoduleSearch();
        $search_input = Yii::$app->request->get('search_input');
        $params = [];
        Yii::$app->request->queryParams;
        if ($search_input) {
            $params['ezm_name'] = $search_input;
        }
        $searchModel->ezm_name = $search_input;
        $dataProvider = $searchModel->search($params);

        return $this->renderAjax('_ezmodules', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEzwidgets() {
        $searchModel = new \backend\modules\ezmodules\models\EzmoduleWidgetSearch();
        $search_input = Yii::$app->request->get('search_input');
        $params = [];
        Yii::$app->request->queryParams;
        if ($search_input) {
            $params['widget_name'] = $search_input;
        }
        $searchModel->widget_name = $search_input;
        $dataProvider = $searchModel->search($params);

        return $this->renderAjax('_ezwidgets', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewEzforms() {
        $searchModel = new EzformSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('_ezforms', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCheckStudydesign() {
        $study_design = Yii::$app->request->get('study_design');
        if (StudyQuery::checkAleadyExistStudy($study_design) == 'true') {
            return 'true';
        }

        return 'false';
    }

    public function actionUpdateStudyTemplate() {
        $module_id = Yii::$app->request->get('ezm_id');
        $check = Yii::$app->request->get('checked');
        $value = $check == 'true' ? '1' : '0';
        $newId = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $user_id = Yii::$app->user->id;

        $maxOrder = 1;
        $sqlMax = " SELECT MAX(ezm_order) as 'maxId' FROM study_templates ";
        $resultMax = Yii::$app->db->createCommand($sqlMax)->queryOne();
        if ($resultMax)
            $maxOrder = $resultMax['maxId'] + 1;

        if ($check == 'true') {
            Yii::$app->db->createCommand()
                    ->insert('study_templates', ['id' => $newId, 'ezm_id' => $module_id, 'ezm_order' => $maxOrder, 'user_id' => $user_id])
                    ->execute();
        } else {
            Yii::$app->db->createCommand()
                    ->delete('study_templates', 'ezm_id=:ezm_id', [':ezm_id' => $module_id])
                    ->execute();
        }
    }

    public function actionUpdateModuleOrder() {
        $module_id = Yii::$app->request->get('ezm_id');
        $module_order = Yii::$app->request->get('order_module');

        $update = Yii::$app->db->createCommand()->update('study_templates', ['ezm_order' => $module_order], 'ezm_id=:ezm_id', [':ezm_id' => $module_id])->execute();
    }

    public function actionUpdateEzform() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezf_version = Yii::$app->request->get('ezf_version');

        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $dynamodb = $query->select('dynamic_db.*')
                ->from('dynamic_db')
                ->where('config_db<>"ncrc" ')
                ->andWhere('rstat <> 3')
                ->all();

        $modelEzform = new \backend\modules\ezforms2\models\Ezform();
        $oldEzform = $modelEzform->findOne(['ezf_id' => $ezf_id, 'ezf_version' => $ezf_version]);
        $oldEzformJson = \appxq\sdii\utils\SDUtility::array2String($oldEzform);
        $zdataTable = $oldEzform->ezf_table;

        $modelChoice = new \backend\modules\ezforms2\models\EzformChoice();
        $ezchoice = $modelChoice->findOne(['ezf_id' => $ezf_id, 'ezf_version' => $ezf_version]);
        $ezchoice = \appxq\sdii\utils\SDUtility::array2String($ezchoice);

        $modelField = new \backend\modules\ezforms2\models\EzformFields();
        $ezfield = $modelField->findOne(['ezf_id' => $ezf_id, 'ezf_version' => $ezf_version]);
        $ezfield_json = \appxq\sdii\utils\SDUtility::array2String($ezfield);

        $modelCondition = new \backend\modules\ezforms2\models\EzformCondition();
        $ezcondition = $modelCondition->findOne(['ezf_id' => $ezf_id, 'ezf_version' => $ezf_version]);
        $ezf_condition_json = \appxq\sdii\utils\SDUtility::array2String($ezcondition);

        $modelVersion = new \backend\modules\ezforms2\models\EzformVersion();
        $ezversion = $modelVersion->findOne(['ezf_id' => $ezf_id, 'ver_code' => $ezf_version]);
        $ezf_version_json = \appxq\sdii\utils\SDUtility::array2String($ezversion);

        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $nowDate = date('Y-m-d H:i:s');


        \Yii::$app->db->createCommand()->insert('ezform_update_log', ['id' => $id, 'ezf_id' => $ezf_id, 'ezf_json' => $oldEzformJson, 'ezf_field_json' => $ezfield_json, 'ezf_choice_json' => $ezchoice, 'ezf_condition_json' => $ezf_condition_json, 'ezf_version_json' => $ezf_version_json, 'create_date' => $nowDate, 'user_create' => $user_id])
                ->execute();
        $result = 'success';
        $response = [];
        $count = 0;

        foreach ($dynamodb as $val) {
            //if ($count < 100) {
            // ================ Check update column table ========================================
            if (isset($zdataTable)) {
                try {
                    $sql_check = " SELECT DISTINCT COLUMN_NAME IN (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$zdataTable}' 
AND TABLE_SCHEMA = '{$val['dbname']}' ) AS 'CHK_FIELD' ,'{$val['dbname']}' AS 'DBNAME',COLUMN_NAME as 'FIELD',TABLE_SCHEMA as 'DB',COLUMN_TYPE,TABLE_SCHEMA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$zdataTable}' AND TABLE_SCHEMA = 'ncrc' ";
                    $query_check = \Yii::$app->db->createCommand($sql_check)->queryAll();

                    $addColumn = " ALTER TABLE {$zdataTable}";
                    foreach ($query_check as $key => $valChk) {
                        if ($val['CHK_FIELD'] == '0') {
                            $addColumn .= " ADD COLUMN IF NOT EXISTS {$valChk['FIELD']} {$valChk['COLUMN_TYPE']} DEFAULT null ";
                        }
                    }
                    $addColumn .= ";";
                    try {
                        $response['all_success'] += 1;
                        \Yii::$app->db->createCommand($addColumn)->execute();
                    } catch (\yii\db\Exception $e) {
                        $response['all_fail'] += 1;
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    }
                } catch (\yii\db\Exception $e) {
                    //\appxq\sdii\utils\VarDumper::dump($e);
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    $response['all_fail'] += 1;
                }
            }

            //=====================================================================================
            $dsn = \Yii::$app->db->dsn;
            $dbname = explode('=', $dsn);
            $portal_base = $dbname[2];
            
            if (isset($val['dbname']) && $val['dbname'] != '' && $val['dbname'] != $portal_base) {
                $sqlCommand = " 
                    DELETE FROM `{$val['dbname']}`.`ezform` WHERE ezf_id='$ezf_id';
                    DELETE FROM `{$val['dbname']}`.`ezform_condition` WHERE ezf_id='$ezf_id';
                    DELETE FROM `{$val['dbname']}`.`ezform_choice` WHERE ezf_id='$ezf_id';
                    DELETE FROM `{$val['dbname']}`.`ezform_fields` WHERE ezf_id='$ezf_id';
                    DELETE FROM `{$val['dbname']}`.`ezform_version` WHERE ezf_id='$ezf_id' AND ver_code='$ezf_version';

                    REPLACE INTO `{$val['dbname']}`.`ezform` (SELECT * FROM `$portal_base`.`ezform` WHERE ezf_id=:ezf_id1) ;
                    REPLACE INTO `{$val['dbname']}`.`ezform_choice` (SELECT * FROM `$portal_base`.`ezform_choice` WHERE ezf_id=:ezf_id2) ;
                    REPLACE INTO `{$val['dbname']}`.`ezform_fields` (SELECT * FROM `$portal_base`.`ezform_fields` WHERE ezf_id=:ezf_id3) ;
                    REPLACE INTO `{$val['dbname']}`.`ezform_condition` (SELECT * FROM `$portal_base`.`ezform_condition` WHERE ezf_id=:ezf_id4) ;
                    REPLACE INTO `{$val['dbname']}`.`ezform_version` (SELECT * FROM `$portal_base`.`ezform_version` WHERE  ver_code=:ver_code AND ezf_id=:ezf_id5 );
                ";
                $params = [':ezf_id1' => $ezf_id, ':ezf_id2' => $ezf_id, ':ezf_id3' => $ezf_id, ':ezf_id4' => $ezf_id, ':ezf_id5' => $ezf_id, ':ver_code' => $ezf_version];
                $query = \Yii::$app->db->createCommand($sqlCommand, $params);

                // Create table if not exist
                try {
                    //$create = " CREATE TABLE IF NOT EXISTS `{$val['dbname']}`.`{$zdataTable}` AS SELECT * FROM `ncrc`.`{$zdataTable}`";
                    //\Yii::$app->db->createCommand($create)->execute();
                } catch (\yii\db\Exception $e) {
                    //\backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }

                try {
                    $query->execute();
                    $response['success'][] = $val['url'];
                } catch (\yii\db\Exception $e) {
                    $result = 'error';
                    //\backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    $response['fail'][] = $val['url'];
                }
            }
            $count ++;
            //}
        }

        return $this->renderAjax('_form_update_response', ['response' => $response,]);
    }

    public function actionUpdateEzmodule() {
        $ezm_id = Yii::$app->request->get('ezm_id');

        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $dynamodb = $query->select('dynamic_db.*')
                ->from('dynamic_db')
                ->where('config_db<>"ncrc"')
                ->all();

        $modelEzmodule = new \backend\modules\ezmodules\models\Ezmodule();
        $oldEzmodule = $modelEzmodule->findOne(['ezm_id' => $ezm_id]);
        $oldEzmoduleJson = \appxq\sdii\utils\SDUtility::array2String($oldEzmodule);

        $modelEzmoduleTab = new \backend\modules\ezmodules\models\EzmoduleTab();
        $oldEzmoduleTab = $modelEzmoduleTab->findOne(['ezm_id' => $ezm_id]);
        $oldEzmoduleTabJson = \appxq\sdii\utils\SDUtility::array2String($oldEzmoduleTab);

        $modelEzmoduleAddon = new \backend\modules\ezmodules\models\EzmoduleAddon();
        $oldEzmoduleAddon = $modelEzmoduleAddon->findOne(['ezm_id' => $ezm_id]);
        $oldEzmoduleAddonJson = \appxq\sdii\utils\SDUtility::array2String($oldEzmoduleAddon);

        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $nowDate = date('Y-m-d H:i:s');

        \Yii::$app->db->createCommand()->insert('ezmodule_update_log', ['id' => $id, 'ezm_id' => $ezm_id, 'ezm_json' => $oldEzmoduleJson, 'ezm_tab_json' => $oldEzmoduleTabJson, 'ezm_addon_json' => $oldEzmoduleAddonJson, 'create_date' => $nowDate, 'user_create' => $user_id])
                ->execute();

        $result = 'success';
        $response = [];
        $dsn = \Yii::$app->db->dsn;
        $dbname = explode('=', $dsn);
        $portal_base = $dbname[2];
        
        foreach ($dynamodb as $val) {
            if (isset($val['dbname']) && $val['dbname'] != '' && $val['dbname'] != $portal_base) {
                $sqlCommand = " 
                DELETE FROM `{$val['dbname']}`.`ezmodule` WHERE ezm_id='$ezm_id';
                DELETE FROM `{$val['dbname']}`.`ezmodule_tab` WHERE ezm_id='$ezm_id';
                DELETE FROM `{$val['dbname']}`.`ezmodule_addon` WHERE ezm_id='$ezm_id';
                DELETE FROM `{$val['dbname']}`.`ezmodule_widget` WHERE ezm_id='$ezm_id';

                REPLACE INTO `{$val['dbname']}`.`ezmodule` (SELECT * FROM `$portal_base`.`ezmodule` WHERE ezm_id=:ezm_id);
                REPLACE INTO `{$val['dbname']}`.`ezmodule_tab` (SELECT * FROM `$portal_base`.`ezmodule_tab` WHERE ezm_id=:ezm_id1);
                REPLACE INTO `{$val['dbname']}`.`ezmodule_addon` (SELECT * FROM `$portal_base`.`ezmodule_addon` WHERE ezm_id=:ezm_id2);
                REPLACE INTO `{$val['dbname']}`.`ezmodule_widget` (SELECT * FROM `$portal_base`.`ezmodule_widget` WHERE ezm_id=:ezm_id3);";
                $params = [':ezm_id' => $ezm_id, ':ezm_id1' => $ezm_id, ':ezm_id2' => $ezm_id, ':ezm_id3' => $ezm_id];
                $query = \Yii::$app->db->createCommand($sqlCommand, $params);

                try {
                    $query->execute();
                    $response['all_success'] = 0;
                    $response['success'][] = $val['url'];
                } catch (\yii\db\Exception $e) {
                    $result = 'error';
                    $response['fail'][] = $val['url'];
                    $response['all_fail'] = 0;
                    //\backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
            }
        }

        return $this->renderAjax('_form_update_response', ['response' => $response,]);
    }
    
    public function actionRestoreModule(){
        $ezm_id = Yii::$app->request->get('ezm_id');
        $log_id = Yii::$app->request->get('log_id');
        
        $update_log = \Yii::$app->db->createCommand("SELECT * FROM ezmodule_update_log WHERE id='$id' ")->queryOne();
        if($update_log){
            $module = (array)json_decode($update_log['ezm_json']);
            $module_tab = (array)json_decode($update_log['ezm_tab_json']);
            $module_addon = (array)json_decode($update_log['ezm_addon_json']);
            
            $mod_field = "";
            $mod_value = "";
            foreach ($module as $key=> $val){
                if($mod_field == "")$mod_field = $key;
                else $mod_field .= ",".$key;
                
                if($mod_value == "")$mod_value = "'{$val}'";
                else $mod_value .= ",'{$val}'";
                
            }
            
            $tab_field = "";
            $tab_value = "";
            foreach ($module_tab as $key=> $val){
                if($tab_field == "")$tab_field = $key;
                else $tab_field .= ",".$key;
                
                if($tab_value == "")$tab_value = "'{$val}'";
                else $tab_value .= ",'{$val}'";
                
            }
            
            $addon_field = "";
            $addon_value = "";
            foreach ($module_addon as $key=> $val){
                if($addon_field == "")$addon_field = $key;
                else $addon_field .= ",".$key;
                
                if($addon_value == "")$addon_value = "'{$val}'";
                else $addon_value .= ",'{$val}'";
                
            }
            
            \Yii::$app->db->createCommand("REPLACE INTO({$mod_field}) ezmodule VALUES ({$mod_value}) ")->execute();
            \Yii::$app->db->createCommand("REPLACE INTO({$mod_field}) ezmodule VALUES ({$mod_value}) ")->execute();
            \Yii::$app->db->createCommand("REPLACE INTO({$mod_field}) ezmodule VALUES ({$mod_value}) ")->execute();
        }
    }
    
    public function actionRestoreEzform(){
        
    }

}
