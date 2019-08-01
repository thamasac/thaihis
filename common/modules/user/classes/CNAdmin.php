<?php

namespace common\modules\user\classes;
use appxq\sdii\utils\VarDumper;
use common\modules\user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

class CNAdmin {
    public static function queryUser($search="") {
        
        $query = User::find();  
        $query->joinWith(['profile']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        $dataProvider->sort->attributes['firstname'] = [
            'asc' => ['profile.firstname' => SORT_ASC],
            'desc' => ['profile.firstname' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['lastname'] = [
            'asc' => ['profile.lastname' => SORT_ASC],
            'desc' => ['profile.lastname' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sitecode'] = [
            'asc' => ['profile.sitecode' => SORT_ASC],
            'desc' => ['profile.sitecode' => SORT_DESC],
        ];
        if(!empty($search)){
            $query->orFilterWhere(['like','user.username', $search['search_name']]);
            $query->orFilterWhere(['like','profile.firstname', $search['search_name']]);
            $query->orFilterWhere(['like','profile.lastname', $search['search_name']]);
            $query->orFilterWhere(['like','profile.sitecode', $search['search_name']]);
            if(!empty($search['fromdate']) and !empty($search['todate'])){
                $todate=date($search['todate']);
                $todate = date("Y-m-d", strtotime($todate."+1 days"));
                $query->andFilterWhere(['between', 'FROM_UNIXTIME(last_login_at)',$search['fromdate'],$todate]);
            }
        }
        return $dataProvider;
    }

    public static function queryRequestListUser($search="",$sitecode) {
        $sqlCount = "SELECT COUNT(*) FROM user INNER JOIN profile ON profile.user_id = user.id INNER JOIN zdata_site_request ON user_create = user.id  WHERE (zdata_site_request.approve_result IS NULL OR zdata_site_request.approve_result = 0) AND zdata_site_request.rstat < 3 AND target_site IS NOT NULL ";
        if(!Yii::$app->user->can("administrator")){
            $sqlCount .="AND target_site = :sitecode;";
        }
        $count = Yii::$app->db->createCommand($sqlCount, [':sitecode' => $sitecode])->queryScalar();
        $dataProviderSql = 'SELECT user.id,profile.user_id,profile.firstname,profile.sitecode,profile.lastname,zdata_site_request.target_site,zdata_site_request.id as request_id ,zdata_site_request.update_date FROM user INNER JOIN profile ON profile.user_id = user.id INNER JOIN zdata_site_request ON user_create = user.id WHERE (zdata_site_request.approve_result IS NULL OR zdata_site_request.approve_result = 0) AND zdata_site_request.rstat < 3 AND target_site IS NOT NULL ';
        if(!Yii::$app->user->can("administrator")){
            $dataProviderSql .="AND target_site = :sitecode;";
        }
        $dataProvider = new SqlDataProvider([
            'sql' => $dataProviderSql,
            'params' => [":sitecode" => $sitecode],
            'totalCount' => $count,
            'sort' => [
                'defaultOrder' => ['zdata_site_request.update_date'=>SORT_DESC],
                'attributes' => [
                    'zdata_site_request.update_date',
                    'profile.firstname',
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $dataProvider;
    }

}
