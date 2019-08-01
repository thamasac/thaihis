<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformChangeLog;

/**
 * EzformChangeLogSearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformChangeLog`.
 */
class EzformChangeLogSearch extends EzformChangeLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'ezf_id', 'ezf_field_id', 'log_count', 'log_ref_id', 'created_by', 'updated_by'], 'integer'],
            [['ezf_version', 'log_type', 'log_event', 'log_detail', 'log_ref_table', 'log_ref_version', 'log_ref_varname', 'created_at', 'updated_at'], 'safe'],
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
        $query = EzformChangeLog::find();

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
            'log_id' => $this->log_id,
            'ezf_id' => $this->ezf_id,
            'ezf_field_id' => $this->ezf_field_id,
            'log_count' => $this->log_count,
            'log_ref_id' => $this->log_ref_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'ezf_version', $this->ezf_version])
            ->andFilterWhere(['like', 'log_type', $this->log_type])
            ->andFilterWhere(['like', 'log_event', $this->log_event])
            ->andFilterWhere(['like', 'log_detail', $this->log_detail])
            ->andFilterWhere(['like', 'log_ref_table', $this->log_ref_table])
            ->andFilterWhere(['like', 'log_ref_version', $this->log_ref_version])
            ->andFilterWhere(['like', 'log_ref_varname', $this->log_ref_varname]);

        return $dataProvider;
    }
}
