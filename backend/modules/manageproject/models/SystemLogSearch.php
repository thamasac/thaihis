<?php

namespace backend\modules\manageproject\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\manageproject\models\SystemLog;

/**
 * SystemLogSearch represents the model behind the search form about `backend\modules\manageproject\models\SystemLog`.
 */
class SystemLogSearch extends SystemLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['create_date', 'create_by', 'action', 'detail'], 'safe'],
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
        $dataProvider=[];
        $query = SystemLog::find();
        $query->joinWith(['profiles as p']);
        $query->orderBy(['create_date'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            //'create_date' => $this->create_date,
        ]);

        $query->andFilterWhere(['like', 'create_by', $this->create_by])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->orFilterWhere(['like', 'p.name', $this->create_by]);    
    //\appxq\sdii\utils\VarDumper::dump($this->create_by);
        if(!empty($this->create_date) && strpos($this->create_date, '-') !== false) {
            list($start_date, $end_date) = explode(' - ', $this->create_date);
            $start_date = $start_date.' 00:00:00';
            $end_date = $end_date.' 23:00:00';
            $query->andFilterWhere(['between', 'create_date', $start_date, $end_date]);
        }

        return $dataProvider;
    }
}
