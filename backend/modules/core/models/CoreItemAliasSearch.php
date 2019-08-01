<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\CoreItemAlias;

/**
 * CoreItemAliasSearch represents the model behind the search form about `backend\modules\core\models\CoreItemAlias`.
 */
class CoreItemAliasSearch extends CoreItemAlias {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['item_code', 'item_name', 'item_data'], 'safe'],
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
		$query = CoreItemAlias::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere(['like', 'item_code', $this->item_code])
				->andFilterWhere(['like', 'item_name', $this->item_name])
				->andFilterWhere(['like', 'item_data', $this->item_data]);

		return $dataProvider;
	}

}
