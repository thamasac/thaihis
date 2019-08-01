<?php

namespace backend\modules\core\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\core\models\CoreTerms;
use backend\modules\core\classes\CoreFunc;
use backend\modules\core\classes\CoreQuery;

/**
 * CoreTermsSearch represents the model behind the search form about `backend\modules\core\models\CoreTerms`.
 */
class CoreTermsSearch extends CoreTerms
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['term_id', 'term_group'], 'integer'],
            [['name', 'slug'], 'safe'],
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
        $query = CoreTerms::find();

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
            'term_id' => $this->term_id,
            'term_group' => $this->term_group,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
    
    public function dataProvider($params, $taxonomy) {
	$dataProvider = [];
	$this->load($params);
	
	if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
	
	if (!in_array($taxonomy, Yii::$app->controller->module->noParentTag)) {
	    $dataProvider = CoreFunc::getTaxonomyDataProvider($taxonomy, $this->name);
	} else {
	    $dataProvider = CoreQuery::getTaxonomyDataProvider($this->name, $taxonomy);
	}
	
	return $dataProvider;
    }
}
