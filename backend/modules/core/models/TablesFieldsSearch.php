<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\TablesFields;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * TablesFieldsSearch represents the model behind the search form about `backend\modules\core\models\TablesFields`.
 */
class TablesFieldsSearch extends TablesFields {

	public function behaviors() {
		return [
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_INIT => 'input_field',
				],
				'value' => function ($event) {
					return NULL;
				},
			],
		];
	}
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['table_id', 'input_required', 'input_order', 'updated_by', 'created_by'], 'integer'],
			[['table_name', 'table_varname', 'table_field_type', 'table_length', 'table_default', 'table_index', 'input_field', 'input_label', 'input_hint', 'input_specific', 'input_data', 'input_validate', 'input_meta', 'updated_at', 'created_at'], 'safe'],
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
		$query = TablesFields::find();

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
			'table_id' => $this->table_id,
			'input_required' => $this->input_required,
			'input_order' => $this->input_order,
			'updated_at' => $this->updated_at,
			'updated_by' => $this->updated_by,
			'created_at' => $this->created_at,
			'created_by' => $this->created_by,
		]);

		$query->andFilterWhere(['like', 'table_name', $this->table_name])
				->andFilterWhere(['like', 'table_varname', $this->table_varname])
				->andFilterWhere(['like', 'table_field_type', $this->table_field_type])
				->andFilterWhere(['like', 'table_length', $this->table_length])
				->andFilterWhere(['like', 'table_default', $this->table_default])
				->andFilterWhere(['like', 'table_index', $this->table_index])
				->andFilterWhere(['like', 'input_field', $this->input_field])
				->andFilterWhere(['like', 'input_label', $this->input_label])
				->andFilterWhere(['like', 'input_hint', $this->input_hint])
				->andFilterWhere(['like', 'input_specific', $this->input_specific])
				->andFilterWhere(['like', 'input_data', $this->input_data])
				->andFilterWhere(['like', 'input_validate', $this->input_validate])
				->andFilterWhere(['like', 'input_meta', $this->input_meta]);

		return $dataProvider;
	}

}
