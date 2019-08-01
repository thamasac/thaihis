<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformCommunity;

/**
 * EzformCommunitySearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformCommunity`.
 */
class EzformCommunitySearch extends EzformCommunity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'send_to', 'object_id', 'dataid', 'query_tool', 'approv_by', 'approv_status', 'status', 'created_by', 'updated_by'], 'integer'],
            [['type', 'content', 'field', 'value_old', 'value_new', 'approv_date', 'created_at', 'updated_at', 'ezf_name'], 'safe'],
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
        $query = EzformCommunity::find()->groupBy(['object_id', 'dataid'])->select([
            'ezform_community.*',
            'ezform.ezf_name',
        ])->innerJoin('ezform', 'ezform.ezf_id=ezform_community.object_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => ['created_at'=>SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ezform_community.id' => $this->id,
            'ezform_community.parent_id' => $this->parent_id,
            'ezform_community.send_to' => $this->send_to,
            'ezform_community.object_id' => $this->object_id,
            'ezform_community.dataid' => $this->dataid,
            'ezform_community.query_tool' => $this->query_tool,
            'ezform_community.approv_by' => $this->approv_by,
            'ezform_community.approv_date' => $this->approv_date,
            'ezform_community.approv_status' => $this->approv_status,
            'ezform_community.status' => $this->status,
            'ezform_community.created_by' => $this->created_by,
            'ezform_community.created_at' => $this->created_at,
            'ezform_community.updated_by' => $this->updated_by,
            'ezform_community.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'ezform_community.type', $this->type])
            ->andFilterWhere(['like', 'ezform_community.content', $this->content])
            ->andFilterWhere(['like', 'ezform_community.field', $this->field])
            ->andFilterWhere(['like', 'ezform_community.value_old', $this->value_old])
            ->andFilterWhere(['like', 'ezform_community.value_new', $this->value_new])
            ->andFilterWhere(['like', 'ezform.ezf_name', $this->ezf_name]);

        return $dataProvider;
    }
}
