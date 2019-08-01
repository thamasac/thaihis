<?php

namespace backend\modules\myworkbench\controllers;

use yii\web\Controller;
use Yii;
use yii\db\Exception;
use backend\modules\ezmodules\models\Ezmodule;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleSearch;
use backend\modules\study_manage\classes\StudyQuery;
use backend\modules\study_manage\models\StudyModuleSearch;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezforms2\models\EzformSearch;

/**
 * Default controller for the `study_manage` module
 */
class MyworkbenchController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $userId = Yii::$app->user->id;
        
        $modelAssignModules = ModuleQuery::getAssignModule($userId);
        $modelFavoriteModules = ModuleQuery::getFavModule($userId);
        $searchModel = new EzformSearch();
        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, '4');
        $modelAssignForm = $dataProvider->getModels();
        
        $dataProvider2 = $searchModel->searchMyForm(Yii::$app->request->queryParams, '5');
        $modelFavoriteForm = $dataProvider2->getModels();
        
        return $this->render('index', [
            'modelAssignModules'=>$modelAssignModules,
            'modelFavoriteModules'=>$modelFavoriteModules,
            'modelAssignForm'=>$modelAssignForm,
            'modelFavoriteForm'=>$modelFavoriteForm,
        ]);
    }

    public function actionAssignView() {
        
    }

    public function actionFavoriteView() {
       
    }

}
