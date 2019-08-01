<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\QueueLog;

/**
 * QueueLogSearch represents the model behind the search form about `backend\modules\ezforms2\models\QueueLog`.
 */
class QueueLogSearch extends QueueLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ezf_id', 'dataid', 'enable', 'setting_id', 'module_id', 'user_receive', 'updated_by', 'created_by'], 'integer'],
            [['unit', 'status', 'current_unit', 'time_receive', 'options', 'updated_at', 'created_at', 'suser_name', 'ruser_name', 'sunit_name', 'module_name', 'tab_name', 'process_forms'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $userProfile = Yii::$app->user->identity->profile;
        $query = QueueLog::find()->select([
            'queue_log.*',
            'CONCAT(suser.firstname, " ", suser.lastname) AS suser_name',
            'CONCAT(ruser.firstname, " ", ruser.lastname) AS ruser_name',
            'sunit.unit_name AS sunit_name',
            'm.ezm_name AS module_name',
            'ezform.ezf_table',
            'ezform.field_detail',
            's.complete_cond',
            's.process_forms'
        ])
                ->where('queue_log.`enable`=1 AND queue_log.unit=:unit', [':unit'=>$userProfile->department])
                ->leftJoin('zdata_working_unit_setting s', 'queue_log.type = "receive" AND s.id = queue_log.setting_id')
                ->leftJoin('ezmodule m', 'm.ezm_id = queue_log.module_id')
                ->innerJoin('zdata_working_unit sunit', 'sunit.id = queue_log.current_unit')
                ->innerJoin('profile suser', 'suser.user_id = queue_log.created_by')
                ->innerJoin('ezform', 'ezform.ezf_id = queue_log.ezf_id')
                ->leftJoin('profile ruser', 'ruser.user_id = queue_log.user_receive');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
            
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'queue_log.id' => $this->id,
            'queue_log.ezf_id' => $this->ezf_id,
            'queue_log.dataid' => $this->dataid,
            'queue_log.enable' => $this->enable,
            'queue_log.setting_id' => $this->setting_id,
            'queue_log.module_id' => $this->module_id,
            'queue_log.user_receive' => $this->user_receive,
            'queue_log.time_receive' => $this->time_receive,
            'queue_log.updated_by' => $this->updated_by,
            'queue_log.updated_at' => $this->updated_at,
            'queue_log.created_by' => $this->created_by,
            //'queue_log.dataid_receive' => $this->dataid_receive,
        ]);
        
        if (isset($this->created_at) && !empty($this->created_at)) {
            $daterang = explode(' to ', $this->created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date(queue_log.created_at)", $sdate, $edate]);
            }
        }

        $query->andFilterWhere(['like', 'queue_log.unit', $this->unit])
            ->andFilterWhere(['like', 'queue_log.status', $this->status])
            ->andFilterWhere(['like', 'queue_log.current_unit', $this->current_unit])
                ->andFilterWhere(['like', 'CONCAT(suser.firstname, " ", suser.lastname)', $this->suser_name])
                ->andFilterWhere(['like', 'sunit.unit_name', $this->sunit_name])
                ->andFilterWhere(['like', 'queue_log.tab_name', $this->tab_name])
            ->andFilterWhere(['like', 'queue_log.options', $this->options]);

        return $dataProvider;
    }
    
    public function searchOut($params)
    {
        $userProfile = Yii::$app->user->identity->profile;
        $query = QueueLog::find()->select([
            'queue_log.*',
            'CONCAT(suser.firstname, " ", suser.lastname) AS suser_name',
            'CONCAT(ruser.firstname, " ", ruser.lastname) AS ruser_name',
            'sunit.unit_name AS sunit_name',
            'm.ezm_name AS module_name',
            'ezform.ezf_table',
            'ezform.field_detail',
            's.complete_cond',
            's.process_forms'
        ])
                ->where('queue_log.`enable`=1 AND queue_log.current_unit=:unit', [':unit'=>$userProfile->department])
                ->innerJoin('zdata_working_unit_setting s', 's.id = queue_log.setting_id')
                ->leftJoin('ezmodule m', 'm.ezm_id = queue_log.module_id')
                ->innerJoin('zdata_working_unit sunit', 'sunit.id = queue_log.unit')
                ->innerJoin('profile suser', 'suser.user_id = queue_log.created_by')
                ->innerJoin('ezform', 'ezform.ezf_id = queue_log.ezf_id')
                ->leftJoin('profile ruser', 'ruser.user_id = queue_log.user_receive');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
            
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'queue_log.id' => $this->id,
            'queue_log.ezf_id' => $this->ezf_id,
            'queue_log.dataid' => $this->dataid,
            'queue_log.enable' => $this->enable,
            'queue_log.setting_id' => $this->setting_id,
            'queue_log.module_id' => $this->module_id,
            'queue_log.user_receive' => $this->user_receive,
            'queue_log.time_receive' => $this->time_receive,
            'queue_log.updated_by' => $this->updated_by,
            'queue_log.updated_at' => $this->updated_at,
            'queue_log.created_by' => $this->created_by,
            //'queue_log.dataid_receive' => $this->dataid_receive,
        ]);
        
        if (isset($this->created_at) && !empty($this->created_at)) {
            $daterang = explode(' to ', $this->created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date(queue_log.created_at)", $sdate, $edate]);
            }
        }

        $query->andFilterWhere(['like', 'queue_log.unit', $this->unit])
            ->andFilterWhere(['like', 'queue_log.status', $this->status])
            ->andFilterWhere(['like', 'queue_log.current_unit', $this->current_unit])
                ->andFilterWhere(['like', 'CONCAT(suser.firstname, " ", suser.lastname)', $this->suser_name])
                ->andFilterWhere(['like', 'sunit.unit_name', $this->sunit_name])
                ->andFilterWhere(['like', 'queue_log.tab_name', $this->tab_name])
            ->andFilterWhere(['like', 'queue_log.options', $this->options]);

        return $dataProvider;
    }
}
