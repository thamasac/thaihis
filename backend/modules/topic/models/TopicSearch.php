<?php

namespace backend\modules\topic\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\Topic;

/**
 * TopicSearch represents the model behind the search form about `backend\modules\ezforms2\models\Topic`.
 */
class TopicSearch extends Topic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'module_id', 'widget_id', 'create_by', 'update_by', 'rstat'], 'integer'],
            [['name', 'detail', 'create_at', 'update_at'], 'safe'],
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
        $query = Topic::find();

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
            'module_id' => $this->module_id,
            'widget_id' => $this->widget_id,
            'create_by' => $this->create_by,
            'create_at' => $this->create_at,
            'update_by' => $this->update_by,
            'update_at' => $this->update_at,
            'rstat' => $this->rstat,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'detail', $this->detail]);

        return $dataProvider;
    }
}
