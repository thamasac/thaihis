<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\CNProfileTcc;

/**
 * ProfileTccSearch represents the model behind the search form about `backend\modules\ezforms2\models\ProfileTcc`.
 */
class CNProfileTccSearch extends CNProfileTcc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
         
        return [
            [['user_id'], 'integer'],
            [['name', 'public_email', 'gravatar_email', 'gravatar_id', 'location', 'website', 'bio', 'title', 'dob', 'timezone', 'sitecode', 'firstname', 'lastname', 'department', 'position', 'avatar_path', 'avatar_base_url', 'certificate'], 'safe'],
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
        $query = CNProfileTcc::find();

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
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'public_email', $this->public_email])
            ->andFilterWhere(['like', 'gravatar_email', $this->gravatar_email])
            ->andFilterWhere(['like', 'gravatar_id', $this->gravatar_id])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'bio', $this->bio])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'dob', $this->dob])
            ->andFilterWhere(['like', 'timezone', $this->timezone])
            ->andFilterWhere(['like', 'sitecode', $this->sitecode])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'avatar_path', $this->avatar_path])
            ->andFilterWhere(['like', 'avatar_base_url', $this->avatar_base_url])
            ->andFilterWhere(['like', 'certificate', $this->certificate]);

        return $dataProvider;
    }
}
