<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformAutonum;

/**
 * EzformAutonumSearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformAutonum`.
 */
class EzformAutonumSearch extends EzformAutonum
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ezf_id', 'ezf_field_id', 'digit', 'count', 'status', 'created_by', 'updated_by', 'per_time'], 'integer'],
            [['label', 'prefix', 'suffix', 'created_at', 'updated_at'], 'safe'],
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
        $query = EzformAutonum::find();

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
            'ezf_id' => $this->ezf_id,
            'ezf_field_id' => $this->ezf_field_id,
            'digit' => $this->digit,
            'count' => $this->count,
            'per_time' => $this->per_time,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'suffix', $this->suffix]);

        return $dataProvider;
    }
}
