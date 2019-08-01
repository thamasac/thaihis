<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;

class TmfBackupController extends Controller {

    public function actionView() {
        $ezf_id = Yii::$app->request->get('ezf_id', '');
        $field_label = Yii::$app->request->get('field_label', '');
        $field_value = Yii::$app->request->get('field_value', '');
        $docTypeId = Yii::$app->request->get('docTypeId', '');
        $docNameId = Yii::$app->request->get('docNameId', '');
        $docDetailId = Yii::$app->request->get('docDetailId', '');
        $modal = Yii::$app->request->get('modal', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');
        $pageSize = Yii::$app->request->get('pageSize', '');
        $field_column = Yii::$app->request->get('field_column', '');
        $field_taget = Yii::$app->request->get('field_taget', '');

        $ezf_table = EzfQuery::getFormTableName($ezf_id);

        $query = new \yii\db\Query();
        $result = $query->select('id,' . $field_value . ',' . $field_label)
                ->from($ezf_table['ezf_table'])
                ->all();
        $items = [];
        $items[] = [
            'label' => 'Document Log',
            'headerOptions' => ['data-value' => '0', 'class' => 'tabHeader'],
            'content' => '',
        ];
        foreach ($result as $key => $val) {
            $items[] = [
                'label' => $val['F2v1'] != '' ? $val['F2v1'] : '',
                'headerOptions' => ['data-value' => $val['id'], 'class' => 'tabHeader'],
                'content' => '',
            ];
        }
        return $this->renderAjax('_view_backup', [
                    'items' => $items,
                    'ezf_id' => $ezf_id,
                    'docTypeId' => $docTypeId,
                    'docNameId' => $docNameId,
                    'docDetailId' => $docDetailId,
                    'field_value' => $field_value,
                    'field_label' => $field_label,
                    'modal' => $modal,
                    'reloadDiv' => $reloadDiv,
                    'pageSize' => $pageSize,
                    'field_column' => $field_column,
                    'field_taget' => $field_taget
        ]);

//        return TabsX::widget([
//            'id'=>'eztabs',
//            'items'=>$items,
//        ]);
    }

    public function actionGetGrid() {
        $id = Yii::$app->request->get('typeId', '');
        $ezf_id = $column = Yii::$app->request->get('ezf_id', '');
        $column = Yii::$app->request->get('column', []);
        $field_taget = Yii::$app->request->get('field_taget', []);
        $pageSize = Yii::$app->request->get('pageSize', 20);
        $name_ezf = Yii::$app->request->get('name_ezf', '');
        $detail_ezf = Yii::$app->request->get('detail_ezf', '');
        $type_ezf = Yii::$app->request->get('type_ezf', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');

//         $column = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($column);
        $ezform = EzfQuery::getFormTableName($ezf_id);
        $searchModel = NULL;
        $dataProvider = NULL;
        $searchModel = new \backend\modules\ezforms2\models\TbdataAll();
        $searchModel->setTableName($ezform['ezf_table']);
        $column[] = 'target';
        $query = new Query();
        if ($id == '0') {
//            $result = $query->select('*')
//                    ->from($ezform['ezf_table'])
//                    ->groupBy(["var_21"]);
            $result = $query->select('`*`')
                    ->from('`zdata_doc_name`')
                    ->leftJoin('( 
                        SELECT zdata_final.* 
                        FROM zdata_doc_detail as zdata_final 
                        WHERE zdata_final.F2v5=(SELECT max(zde.F2v5) FROM zdata_doc_detail zde WHERE zde.var_21=zdata_final.var_21) 
                        GROUP BY zdata_final.var_21 
                        )as zdata_doc_detail', 'zdata_doc_name.id=zdata_doc_detail.var_21');
//                    ->all();
        } else {
//            $result = $query->select($column)
//                    ->from($ezform['ezf_table'])
//                    ->where("{$field_taget} = :{$field_taget}", [":{$field_taget}" => $id])
//                    ->groupBy(["{$field_taget}"])
//                    ->orderBy(['F2v5' => SORT_DESC]);
            $result = $query->select(['zdata_doc_name.*'])
                    ->from('`zdata_doc_name`')
                    ->leftJoin('( 
                        SELECT zdata_final.* 
                        FROM zdata_doc_detail as zdata_final 
                        WHERE zdata_final.F2v5=(SELECT max(zde.F2v5) FROM zdata_doc_detail zde WHERE zde.var_21=zdata_final.var_21) 
                        GROUP BY zdata_final.var_21 
                        )as zdata_doc_detail', 'zdata_doc_name.id=zdata_doc_detail.var_21')
                    ->where("zdata_doc_name.target = :target", [":target" => $id])
                    ->groupBy(['zdata_doc_detail.var_21']);
//                    ->all();
        }
//        \appxq\sdii\utils\VarDumper::dump($result);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $result,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => $column
            ],
        ]);



        return $this->renderAjax('_grid_backup', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'ezf_id' => $ezf_id,
                    'field_taget' => $field_taget,
                    'column' => $column,
                    'name_ezf' => $name_ezf,
                    'detail_ezf' => $detail_ezf,
                    'type_id' => $id,
                    'reloadDiv' => $reloadDiv,
                    'type_ezf' => $type_ezf
        ]);
    }

    public function actionGetSubGrid() {
        $id = Yii::$app->request->get('id', '');
        $ezf_id = $column = Yii::$app->request->get('ezf_id', '');
        $column = Yii::$app->request->get('column', []);
        $field_taget = Yii::$app->request->get('field_taget', '');
        $pageSize = Yii::$app->request->get('pageSize', 20);
        $detail_ezf = Yii::$app->request->get('detail_ezf');
        $target = Yii::$app->request->get('target', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');

        $ezf_table = EzfQuery::getFormTableName($ezf_id);
        $column[] = 'id';
        $query = new Query();

        $result = $query->select($column)
                ->from($ezf_table['ezf_table'])
                ->where("{$field_taget} = :{$field_taget}", [":{$field_taget}" => $id])
                ->orderBy(['F2v5' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $result,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => $column
            ],
        ]);

        return $this->renderAjax('_sub-grid_backup', [
                    'dataProvider' => $dataProvider,
                    'ezf_id' => $ezf_id,
                    'field_taget' => $field_taget,
                    'column' => $column,
                    'id' => $id,
                    'detail_ezf' => $detail_ezf,
                    'target' => $target,
                    'reloadDiv' => $reloadDiv
        ]);
    }

}
