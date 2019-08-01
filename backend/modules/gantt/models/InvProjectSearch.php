<?php

namespace backend\modules\gantt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\gantt\models\InvProject;

/**
 * InvProjectSearch represents the model behind the search form about `backend\modules\gantt\models\InvProject`.
 */
class InvProjectSearch extends InvProject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'share', 'approve'], 'integer'],
            [['project'], 'safe'],
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
        $query = InvProject::find();

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
            'id' => $this->id,
            'status' => $this->status,
            'share' => $this->share,
            'approve' => $this->approve,
        ]);

        $query->andFilterWhere(['like', 'project', $this->project]);

        return $dataProvider;
    }
}
