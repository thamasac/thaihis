<?php

namespace backend\modules\linebot\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\linebot\models\LineFunctions;

/**
 * LineFunctionsSearch represents the model behind the search form about `backend\modules\linebot\models\LineFunctions`.
 */
class LineFunctionsSearch extends LineFunctions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active', 'updated_by', 'created_by'], 'integer'],
            [['channel_id', 'command', 'api', 'template', 'options', 'role', 'updated_at', 'created_at'], 'safe'],
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
        $query = LineFunctions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'channel_id', $this->channel_id])
            ->andFilterWhere(['like', 'command', $this->command])
            ->andFilterWhere(['like', 'api', $this->api])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'role', $this->role]);

        return $dataProvider;
    }
}
