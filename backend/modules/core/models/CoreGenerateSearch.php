<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\CoreGenerate;

/**
 * CoreGenerateSearch represents the model behind the search form about `backend\modules\core\models\CoreGenerate`.
 */
class CoreGenerateSearch extends CoreGenerate {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['gen_id', 'updated_by', 'created_by'], 'integer'],
			[['gen_group', 'gen_name', 'gen_tag', 'gen_link', 'gen_process', 'gen_ui', 'template_php', 'template_html', 'template_js', 'updated_at', 'created_at'], 'safe'],
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
		$query = CoreGenerate::find();

		$dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => 100,
                    ],
                ]);

                $this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'gen_id' => $this->gen_id,
			'updated_at' => $this->updated_at,
			'updated_by' => $this->updated_by,
			'created_at' => $this->created_at,
			'created_by' => $this->created_by,
		]);

		$query->andFilterWhere(['like', 'gen_group', $this->gen_group])
				->andFilterWhere(['like', 'gen_name', $this->gen_name])
				->andFilterWhere(['like', 'gen_tag', $this->gen_tag])
				->andFilterWhere(['like', 'gen_link', $this->gen_link])
				->andFilterWhere(['like', 'gen_process', $this->gen_process])
				->andFilterWhere(['like', 'gen_ui', $this->gen_ui])
				->andFilterWhere(['like', 'template_php', $this->template_php])
				->andFilterWhere(['like', 'template_html', $this->template_html])
				->andFilterWhere(['like', 'template_js', $this->template_js]);

		return $dataProvider;
	}

}
