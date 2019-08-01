<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformFields;

/**
 * EzformFieldsSearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformFields`.
 */
class EzformFieldsSearch extends EzformFields {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ezf_field_id', 'ezf_id', 'ezf_field_group', 'ezf_field_type', 'ezf_field_ref', 'ref_ezf_id', 'parent_ezf_id', 'ezf_field_lenght', 'ezf_margin_col', 'ezf_field_required', 'table_field_length', 'ezf_condition', 'ezf_target', 'ezf_special', 'created_by', 'updated_by'], 'integer'],
            [['ref_form','table_index', 'ref_field_id', 'ezf_field_name', 'ezf_field_label', 'ezf_field_default', 'ref_field_desc', 'ref_field_search', 'ezf_field_hint', 'ezf_field_validate', 'ezf_field_data', 'ezf_field_specific', 'ezf_field_options', 'table_field_type', 'ezf_field_color', 'ezf_field_cal', 'created_at', 'updated_at'], 'safe'],
            [['ezf_field_order'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = EzformFields::find();

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
            'ezf_field_id' => $this->ezf_field_id,
            'ezf_id' => $this->ezf_id,
            'ezf_field_group' => $this->ezf_field_group,
            'ezf_field_type' => $this->ezf_field_type,
            'ezf_field_ref' => $this->ezf_field_ref,
            'ref_ezf_id' => $this->ref_ezf_id,
            'parent_ezf_id' => $this->parent_ezf_id,
            'ezf_field_order' => $this->ezf_field_order,
            'ezf_field_lenght' => $this->ezf_field_lenght,
            'ezf_margin_col' => $this->ezf_margin_col,
            'ezf_field_required' => $this->ezf_field_required,
            'table_field_length' => $this->table_field_length,
            'ezf_condition' => $this->ezf_condition,
            'ezf_target' => $this->ezf_target,
            'table_index' => $this->table_index,
            'ezf_special' => $this->ezf_special,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            
        ]);

        $query->andFilterWhere(['like', 'ezf_field_name', $this->ezf_field_name])
                ->andFilterWhere(['like', 'ezf_field_label', $this->ezf_field_label])
                ->andFilterWhere(['like', 'ezf_field_default', $this->ezf_field_default])
                ->andFilterWhere(['like', 'ref_field_desc', $this->ref_field_desc])
                ->andFilterWhere(['like', 'ref_field_search', $this->ref_field_search])
                ->andFilterWhere(['like', 'ref_form', $this->ref_form])
                ->andFilterWhere(['like', 'ezf_field_hint', $this->ezf_field_hint])
                ->andFilterWhere(['like', 'ezf_field_validate', $this->ezf_field_validate])
                ->andFilterWhere(['like', 'ezf_field_data', $this->ezf_field_data])
                ->andFilterWhere(['like', 'ezf_field_specific', $this->ezf_field_specific])
                ->andFilterWhere(['like', 'ezf_field_options', $this->ezf_field_options])
                ->andFilterWhere(['like', 'table_field_type', $this->table_field_type])
                ->andFilterWhere(['like', 'ezf_field_color', $this->ezf_field_color])
                ->andFilterWhere(['like', 'ref_field_id', $this->ref_field_id])
                ->andFilterWhere(['like', 'ezf_field_cal', $this->ezf_field_cal]);

        return $dataProvider;
    }

}
