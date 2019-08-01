<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformInput;

/**
 * EzformInputSearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformInput`.
 */
class EzformInputSearch extends EzformInput
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['input_id', 'table_field_length', 'input_active'], 'integer'],
            [['input_behavior', 'input_name', 'input_class', 'input_function', 'system_class', 'input_data', 'input_validate', 'input_specific', 'input_option', 'table_field_type', 'input_version'], 'safe'],
            [['input_order', 'input_size'], 'number'],
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
        $query = EzformInput::find()->where('input_version="v2"')->orderBy('input_order');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'input_id' => $this->input_id,
            'table_field_length' => $this->table_field_length,
            'input_order' => $this->input_order,
            'input_active' => $this->input_active,
            'input_size' => $this->input_size,
        ]);

        $query->andFilterWhere(['like', 'input_name', $this->input_name])
            ->andFilterWhere(['like', 'input_class', $this->input_class])
            ->andFilterWhere(['like', 'input_function', $this->input_function])
            ->andFilterWhere(['like', 'system_class', $this->system_class])
            ->andFilterWhere(['like', 'input_behavior', $this->input_behavior])  
            ->andFilterWhere(['like', 'input_data', $this->input_data])
            ->andFilterWhere(['like', 'input_validate', $this->input_validate])
            ->andFilterWhere(['like', 'input_specific', $this->input_specific])
            ->andFilterWhere(['like', 'input_option', $this->input_option])
            ->andFilterWhere(['like', 'table_field_type', $this->table_field_type])
            ->andFilterWhere(['like', 'input_version', $this->input_version]);

        return $dataProvider;
    }
}
