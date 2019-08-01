<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\RandomCode;

/**
 * RandomCodeSearch represents the model behind the search form about `backend\modules\ezforms2\models\RandomCode`.
 */
class RandomCodeSearch extends RandomCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'max_index', 'code_index','user_create','ezf_id'], 'integer'],
            [['name', 'code_random', ], 'safe'],
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
        $query = RandomCode::find();

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
            'max_index' => $this->max_index,
            'code_index' => $this->code_index,
            'user_create' => $this->user_create,
            'ezf_id' => $this->ezf_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code_random', $this->code_random])
            ->andFilterWhere(['like', 'user_create', $this->user_create])
            ->andFilterWhere(['like', 'ezf_id', $this->ezf_id]);

        return $dataProvider;
    }
}
