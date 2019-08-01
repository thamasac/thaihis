<?php
/**
 * Created by PhpStorm.
 * User: AR9
 * Date: 9/1/2562
 * Time: 11:12
 */

namespace app\modules\report\controllers;


use appxq\sdii\utils\VarDumper;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;
class OverviewController extends Controller
{
    public function actionIndex()
    {


        $queryProjectType = (new Query())->select([
            'IF(studydesign = 99, "1", "0") AS custom_sort',
            'studydesign',
            'COUNT(projectname) AS nall',
            'count(IF (YEAR (create_date) = YEAR (CURDATE()),studydesign,NULL)) AS thisYear',
            'count(IF (YEAR (create_date) = YEAR (CURDATE())AND MONTH (create_date) = MONTH (CURDATE()),studydesign,NULL)) AS thisMonth',
            'count(IF (YEARWEEK(create_date) = YEARWEEK(NOW()),studydesign,NULL)) AS thisWeek',
            'count(IF (DATE(create_date) = CURDATE(),studydesign,NULL)) AS today'])
            ->from('zdata_create_project')
            ->where('rstat NOT IN (0,3)');
        $data_sum_project_type = $queryProjectType->one();

        $data_getuser = $this->getNumUser('');

        $data_sum_user = ['nall' => 0, 'thisYear' => 0, 'thisMonth' => 0, 'thisWeek' => 0, 'today' => 0];
        if(isset($data_getuser['data_sum_user']) && is_array($data_getuser['data_sum_user']) && !empty($data_getuser['data_sum_user'])){
            $data_sum_user = $data_getuser['data_sum_user'];
        }

        $data_user_dynamic = null;
        if(isset($data_getuser['data_user_dynamic']) && is_array($data_getuser['data_user_dynamic']) && !empty($data_getuser['data_user_dynamic'])){
            $data_user_dynamic = $data_getuser['data_user_dynamic'];
        }

        $dataProvider = new SqlDataProvider([
            'sql' => $queryProjectType->groupBy('studydesign')->orderBy(['custom_sort' => SORT_ASC,'nall' => SORT_DESC])->createCommand()->rawSql,
//            'sort' => [
//                'attributes' => [
//                    'unit_name',
//                    'nall',
//                    'thisYear',
//                    'thisMonth',
//                    'thisWeek',
//                    'today'
//                ],
//                'defaultOrder'=>[
//                    'nall' => SORT_DESC
//                ]
//            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

//        $dataProvider = $queryProjectType->groupBy('studydesign')->orderBy(['custom_sort' => SORT_ASC,'nall' => SORT_DESC])->all();


        $dataProviderUser = new ArrayDataProvider([
            'allModels' => $data_user_dynamic,
            'sort' => [
                'attributes' => [
//                    'unit_name',
                    'nall',
                    'thisYear',
                    'thisMonth',
                    'thisWeek',
                    'today'
                ],
                'defaultOrder' => [
                    'nall' => SORT_DESC
                ]
            ],
            'pagination' => false
//            'pagination' => [
//                'pageSize' => 100,
//            ],
        ]);
//        VarDumper::dump($dataProviderUser);

        return $this->renderAjax('index', [
            'data_sum_user' => $data_sum_user,
            'data_sum_project_type' => $data_sum_project_type,
            'dataProvider' => $dataProvider,
            'dataProviderUser' => $dataProviderUser
        ]);
    }

    public function actionGetNumUser(){
        $type = \Yii::$app->request->get('type','');
        $data_getuser = $this->getNumUser($type);

        $data_user_dynamic = null;
        if(isset($data_getuser['data_user_dynamic']) && is_array($data_getuser['data_user_dynamic']) && !empty($data_getuser['data_user_dynamic'])){
            $data_user_dynamic = $data_getuser['data_user_dynamic'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data_user_dynamic,
            'sort' => [
                'attributes' => [
//                    'unit_name',
                    'nall',
                    'thisYear',
                    'thisMonth',
                    'thisWeek',
                    'today'
                ],
                'defaultOrder' => [
                    'nall' => SORT_DESC
                ]
            ],
            'pagination' => false
//            'pagination' => [
//                'pageSize' => 100,
//            ],
        ]);

        return $this->renderAjax('_get-numuser',[
            'dataProvider'=>$dataProvider,
            'type' => $type
        ]);

    }
    private function getAllProject($type='')
    {
        $dynamicDb = (new Query())->select('*')->from('dynamic_db')
            ->innerJoin('zdata_create_project AS cp','cp.id = dynamic_db.data_id AND cp.rstat NOT IN (0,3)')
            ->where('dynamic_db.id NOT IN (1,2,3,4)')
            ->andWhere('dynamic_db.rstat NOT IN (0,3)');

        if($type != ''){
            $dynamicDb->andWhere(['cp.studydesign' => $type]);
        }
        return $dynamicDb->all();
    }
    private function getNumUser($type=''){
        $dynamicDb = $this->getAllProject($type);
        $data_sum_user = ['nall' => 0, 'thisYear' => 0, 'thisMonth' => 0, 'thisWeek' => 0, 'today' => 0];
        $data_user_dynamic = null;
        if ($dynamicDb && is_array($dynamicDb) && !empty($dynamicDb)) {
            foreach ($dynamicDb as $val_db) {
                if (($val_db['host'] == '' || $val_db['host'] == 'localhost') && !in_array($val_db['id'], [1, 2, 3, 4, 330, 1542342695031477564])) {

                    try {
                        $data = (new Query())->select([
                            'COUNT(id) AS nall',
                            'count(IF (YEAR (from_unixtime(created_at,"%Y-%m-%d")) = YEAR (CURDATE()),id,NULL)) AS thisYear',
                            'count(IF (YEAR (from_unixtime(created_at,"%Y-%m-%d")) = YEAR (CURDATE())AND MONTH (from_unixtime(created_at,"%Y-%m-%d")) = MONTH (CURDATE()),id,NULL)) AS thisMonth',
                            'count(IF (YEARWEEK(from_unixtime(created_at,"%Y-%m-%d")) = YEARWEEK(NOW()),id,NULL)) AS thisWeek',
                            'count(IF (DATE(from_unixtime(created_at,"%Y-%m-%d")) = CURDATE(),id,NULL)) AS today'])
                            ->from($val_db['dbname'] . '.user')
                            ->where('blocked_at = \'\' OR blocked_at is null')
                            ->andWhere('id != 1')
//                            ->groupBy('id')
                            ->one();
                        if ($data) {
                            $data['project_name'] = $val_db['aconym'];
                            $data_sum_user['thisYear'] = (int)$data['thisYear'] + (int)$data_sum_user['thisYear'];
                            $data_sum_user['thisMonth'] = (int)$data['thisMonth'] + (int)$data_sum_user['thisMonth'];
                            $data_sum_user['thisWeek'] = (int)$data['thisWeek'] + (int)$data_sum_user['thisWeek'];
                            $data_sum_user['today'] = (int)$data['today'] + (int)$data_sum_user['today'];
                            $data_sum_user['nall'] = (int)$data_sum_user['nall'] + (int)$data['nall'];
                            $data_user_dynamic[] = $data;
                        }
                    } catch (Exception $ex) {
                    }

                }
            }
        }

        return ['data_sum_user'=>$data_sum_user,'data_user_dynamic' =>$data_user_dynamic];
    }
    public function actionProjectActivity(){
        $dynamicDb = $this->getAllProject('');
        $sum=[];
        if ($dynamicDb && is_array($dynamicDb) && !empty($dynamicDb)) {
            foreach ($dynamicDb as $val_db) {
                if (($val_db['host'] == '' || $val_db['host'] == 'localhost') && !in_array($val_db['id'], [1, 2, 3, 4, 330, 1542342695031477564])) {
                    try {
                        $db= $val_db['dbname'];
                        //`$db`.
                        $sql = "select 
                                sum(FROM_UNIXTIME(last_login_at) between curdate() and DATE_ADD(curdate(), INTERVAL 1 DAY)) as `1day`,
                                sum(FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 7 DAY) and curdate()) as `7day`,
                                sum(FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 1 MONTH) and curdate()) as `1month`,
                                sum(FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 3 MONTH) and curdate()) as `3month`,
                                sum(FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 6 MONTH) and curdate()) as `6month`,
                                sum(FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 1 YEAR) and curdate()) as `1year`
                                from `$db`.`user` where blocked_at ='' OR blocked_at is null";
                        $data = Yii::$app->db->createCommand($sql)->queryOne();
                        $sum['1dayU'] =(int)$sum['1dayU'] + (int)$data['1day'];
                        if($data['1day'] >0){
                            $sum['1dayP']++;
                        }
                        $sum['7dayU'] = (int)$sum['7dayU'] + (int)$data['7day'];
                        if($data['7day'] >0){
                            $sum['7dayP']++;
                        }
                        $sum['1monthU'] =(int)$sum['1monthU'] + (int)$data['1month'];
                        if($data['1month'] >0){
                            $sum['1monthP']++;
                        }
                        $sum['3monthU'] =(int)$sum['3monthU'] + (int)$data['3month'];
                        if($data['3month'] >0){
                            $sum['3monthP']++;
                        }
                        $sum['6monthU'] =(int)$sum['6monthU'] + (int)$data['6month'];
                        if($data['6month'] >0){
                            $sum['6monthP']++;
                        }
                        $sum['1yearU'] =(int)$sum['1yearU'] + (int)$data['1year'];
                        if($data['1year'] >0){
                            $sum['1yearP']++;
                        }

                    } catch (Exception $ex) {
                    }
                }
            }
            return $this->renderAjax('time-activity',[
                'sum' => $sum
            ]);
        }
    }
    public function actionListActivity()
    {
        $type = \Yii::$app->request->get('type','');
        $text = \Yii::$app->request->get('text','');
        $dynamicDb = $this->getAllProject('');
        if ($dynamicDb && is_array($dynamicDb) && !empty($dynamicDb)) {
            foreach ($dynamicDb as $val_db) {
                if (($val_db['host'] == '' || $val_db['host'] == 'localhost') && !in_array($val_db['id'], [1, 2, 3, 4, 330, 1542342695031477564])) {
                    try {
                        $db= $val_db['dbname'];
                        $projectname= $val_db['projectname'];
                        //`$db`.
                        $sql = "select count(id) as `count` from `$db`.`user` where blocked_at ='' OR blocked_at is null ";
                        if($type=='1day'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between curdate() and DATE_ADD(curdate(), INTERVAL 1 DAY)';
                        }elseif($type=='7day'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 7 DAY) and curdate()';
                        }elseif($type=='1month'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 1 MONTH) and curdate()';
                        }elseif($type=='3month'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 3 MONTH) and curdate()';
                        }elseif($type=='6month'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 6 MONTH) and curdate()';
                        }elseif($type=='1year'){
                            $sql.='and FROM_UNIXTIME(last_login_at) between DATE_SUB(curdate(), INTERVAL 1 YEAR) and curdate()';
                        }
                        $query = Yii::$app->db->createCommand($sql)->queryOne();
                        if($query['count']>0){
                            $query['projectname'] = $projectname;
                            $data[] = $query;

                        }
                    } catch (Exception $ex) {
                    }
                }
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);
            return $this->renderAjax('project-activity',[
                'dataProvider' => $dataProvider,
                'text' => $text
            ]);
        }
    }
}