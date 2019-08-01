<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformVersion;

/**
 * EzformVersionSearch represents the model behind the search form about `backend\modules\ezforms2\models\EzformVersion`.
 */
class EzformVersionSearch extends EzformVersion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ver_code', 'ver_for', 'approved_date', 'ver_options', 'field_detail', 'ezf_sql', 'ezf_js', 'ezf_error', 'ezf_options', 'updated_at', 'created_at', 'ezf_name'], 'safe'],
            [['ver_approved', 'ver_active', 'approved_by', 'ezf_id', 'updated_by', 'created_by'], 'integer'],
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
        $query = EzformVersion::find()
                    ->select(["ezform_version.*", "(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_version.approved_by) AS fullname, ezform.ezf_name"])
                    ->innerJoin('ezform', 'ezform.ezf_id = ezform_version.ezf_id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                'created_at' => SORT_DESC
            ]
                ],
            'pagination' => ['pageSize' => 100],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ver_approved' => $this->ver_approved,
            'ver_active' => $this->ver_active,
            'approved_by' => $this->approved_by,
            'approved_date' => $this->approved_date,
            'ezf_id' => $this->ezf_id,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'ver_code', $this->ver_code])
            ->andFilterWhere(['like', 'ver_for', $this->ver_for])
            ->andFilterWhere(['like', 'ezform.ezf_name', $this->ezf_name])    
            ->andFilterWhere(['like', 'ver_options', $this->ver_options])
            ->andFilterWhere(['like', 'ezform_version.field_detail', $this->field_detail])
            ->andFilterWhere(['like', 'ezform_version.ezf_sql', $this->ezf_sql])
            ->andFilterWhere(['like', 'ezform_version.ezf_js', $this->ezf_js])
            ->andFilterWhere(['like', 'ezform_version.ezf_error', $this->ezf_error])
            ->andFilterWhere(['like', 'ezform_version.ezf_options', $this->ezf_options]);

        return $dataProvider;
    }
}
