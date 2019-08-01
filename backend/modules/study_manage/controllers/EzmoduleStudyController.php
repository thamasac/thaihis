<?php

namespace backend\modules\study_manage\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\manage_modules\classes\ManageModuleFunc;
use yii\db\Exception;
use backend\modules\ezmodules\models\Ezmodule;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleSearch;
use backend\modules\study_manage\classes\StudyQuery;
use backend\modules\study_manage\models\StudyModuleSearch;

/**
 * Default controller for the `study_manage` module
 */
class EzmoduleStudyController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $searchModel = new StudyModuleSearch();
        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);
        $studyAll = StudyQuery::getStudyDesign();

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'studyAll' => $studyAll,
        ]);
    }

    public function actionView() {
        $searchModel = new EzmoduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $studyAll = StudyQuery::getStudyDesign();

        return $this->renderAjax('_view', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'studyAll' => $studyAll,
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
        
        $maxOrder=1;
        $sqlMax = " SELECT MAX(ezm_order) as 'maxId' FROM study_templates ";
        $resultMax = Yii::$app->db->createCommand($sqlMax)->queryOne();
        if($resultMax)$maxOrder=$resultMax['maxId']+1;
        
        if ($check == 'true') {
            Yii::$app->db->createCommand()
                    ->insert('study_templates', ['id' => $newId,'ezm_id' => $module_id,'ezm_order' => $maxOrder,'user_id'=>$user_id])
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

        $update = Yii::$app->db->createCommand()->update('study_templates', ['ezm_order'=>$module_order],'ezm_id=:ezm_id',[':ezm_id'=>$module_id])->execute();
    }

}
