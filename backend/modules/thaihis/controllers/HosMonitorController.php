<?php
/**
 * Created by PhpStorm.
 * User: AR9
 * Date: 21/12/2561
 * Time: 11:09
 */

namespace backend\modules\thaihis\controllers;


use appxq\sdii\utils\VarDumper;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\web\Controller;

class HosMonitorController extends Controller
{

    public function actionIndex()
    {
        $dataWorking = (new Query())
            ->select(['wu.unit_name',
	'COUNT(wu.unit_name) AS nall',
	'count(IF (YEAR (v.visit_date) = YEAR (CURDATE()),wu.unit_name,NULL)) AS thisYear',
	'count(IF (YEAR (v.visit_date) = YEAR (CURDATE())AND MONTH (v.visit_date) = MONTH (CURDATE()),wu.unit_name,NULL)) AS thisMonth',
	'count(IF (YEARWEEK(v.visit_date) = YEARWEEK(NOW()),wu.unit_name,NULL)) AS thisWeek',
	'count(IF (DATE(v.visit_date) = CURDATE(),wu.unit_name,NULL)) AS today'])->from('zdata_working_unit AS wu')
            ->innerJoin('zdata_order_type AS ot', 'ot.id=wu.unit_order_type')
            ->innerJoin('zdata_visit_tran AS vt','vt.visit_tran_dept = wu.id')
            ->innerJoin('zdata_visit AS v','v.id = vt.visit_tran_visit_id')
            ->where('wu.rstat NOT IN (0,3) AND wu.unit_order_type != 1536740281005084000');

        $dataWorkingAll = $dataWorking->one();

        $dataProvider = new SqlDataProvider([
            'sql' => $dataWorking->groupBy('wu.unit_name')->createCommand()->rawSql,
            'sort' => [
                'attributes' => [
//                    'unit_name',
                    'nall',
                    'thisYear',
                    'thisMonth',
                    'thisWeek',
                    'today'
                ],
                'defaultOrder'=>[
                    'nall' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 200
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'dataWorkingAll' => $dataWorkingAll
        ]);
    }

}