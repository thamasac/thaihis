<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\CoreOptions;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * CoreOptionsSearch represents the model behind the search form about `backend\modules\core\models\CoreOptions`.
 */
class CoreOptionsSearch extends CoreOptions {

	public function behaviors() {
		return [
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_INIT => 'autoload',
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
			[['option_id', 'input_required', 'input_order'], 'integer'],
			[['option_name', 'option_value', 'autoload', 'input_label', 'input_hint', 'input_field', 'input_specific', 'input_data', 'input_validate', 'input_meta'], 'safe'],
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
		$query = CoreOptions::find();

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
			'option_id' => $this->option_id,
			'input_required' => $this->input_required,
			'input_order' => $this->input_order,
		]);

		$query->andFilterWhere(['like', 'option_name', $this->option_name])
				->andFilterWhere(['like', 'option_value', $this->option_value])
				->andFilterWhere(['like', 'autoload', $this->autoload])
				->andFilterWhere(['like', 'input_label', $this->input_label])
				->andFilterWhere(['like', 'input_hint', $this->input_hint])
				->andFilterWhere(['like', 'input_field', $this->input_field])
				->andFilterWhere(['like', 'input_specific', $this->input_specific])
				->andFilterWhere(['like', 'input_data', $this->input_data])
				->andFilterWhere(['like', 'input_validate', $this->input_validate])
				->andFilterWhere(['like', 'input_meta', $this->input_meta]);

		return $dataProvider;
	}

}
