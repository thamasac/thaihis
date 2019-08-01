<?php

namespace backend\modules\proposal\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\manage_modules\classes\ManageModuleFunc;
use yii\db\Exception;
use backend\modules\ezmodules\models\Ezmodule;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleSearch;
use backend\modules\proposal\classes\ProposalQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;

/**
 * Default controller for the `study_manage` module
 */
class ProposalController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $options = Yii::$app->request->get('options');
        $proposal_form = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options['proposal_ezf_id']);
        
        
        
        $data = SubjectManagementQuery::GetTableQuery($proposal_form);
         $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                //'create_date' => SORT_DESC
                ]
            ]
        ]);
        return $this->renderAjax('index', [
            'dataProvider'=>$dataProvider,
            'options'=>$options,
            'display_column'=> $options['display_fields'],
        ]);
    }


}
