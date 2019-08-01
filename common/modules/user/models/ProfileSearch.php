<?php

namespace common\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use backend\modules\core\classes\CoreFunc;
use backend\modules\core\classes\CoreQuery;
/**
 * TbQuestionnaireSearch represents the model behind the search form about `backend\modules\app\models\TbQuestionnaire`.
 */
class ProfileSearch extends Profile
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
	$rules = [
	    [['user_id'], 'integer'],
            [['bio', 'public_email', 'gravatar_email', 'gravatar_id', 'website', 'name', 'location'], 'safe'],
        ];
	
	return ArrayHelper::merge($rules, CoreFunc::getTableRulesSearch('profile'));
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
    public function search($params, $type, $dynamicFields)
    {
        $query = Profile::find()
		->select('profile.*, user.blocked_at')
		->innerJoin('user', 'user.id=profile.user_id')
		->where('utype=:utype', [':utype'=>$type]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

	$query->andFilterWhere(['like', 'bio', $this->bio])
            ->andFilterWhere(['like', 'public_email', $this->public_email])
            ->andFilterWhere(['like', 'gravatar_email', $this->gravatar_email])
	    ->andFilterWhere(['like', 'name', $this->name])
	    ->andFilterWhere(['like', 'gravatar_id', $this->gravatar_id])
	    ->andFilterWhere(['like', 'location', $this->location])
	    ->andFilterWhere(['like', 'website', $this->website]);
	
	$andFilterWhere = [
	    'user_id' => $this->user_id,
	];
	
	$dynamicFields = $this->dynamicFields;
	foreach ($dynamicFields as $key => $value) {
	    $varname = $value['table_varname'];
	    if(in_array(strtoupper($value['table_field_type']), ['INT', 'DATE', 'DATETIME', 'DOUBLE', 'TINYINT'])) {
		$andFilterWhere[$varname] = $this->$varname;
	    } else {
		 $query->andFilterWhere(['like', $varname, $this->$varname]);
	    }
	}
	
        $query->andFilterWhere($andFilterWhere);

        return $dataProvider;
    }
}
