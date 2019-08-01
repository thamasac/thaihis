<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\CoreFields;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * CoreFieldsSearch represents the model behind the search form about `backend\modules\core\models\CoreFields`.
 */
class CoreFieldsSearch extends CoreFields {

	public function behaviors() {
		return [
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_INIT => 'field_internal',
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
			[['field_code', 'field_class', 'field_name', 'field_meta', 'field_description'], 'safe'],
			[['field_internal'], 'integer'],
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
		$query = CoreFields::find();

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
			'field_internal' => $this->field_internal,
		]);

		$query->andFilterWhere(['like', 'field_code', $this->field_code])
				->andFilterWhere(['like', 'field_class', $this->field_class])
				->andFilterWhere(['like', 'field_name', $this->field_name])
				->andFilterWhere(['like', 'field_meta', $this->field_meta])
				->andFilterWhere(['like', 'field_description', $this->field_meta]);

		return $dataProvider;
	}

}
