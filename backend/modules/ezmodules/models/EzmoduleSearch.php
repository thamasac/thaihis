<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\Ezmodule;

/**
 * EzmoduleSearch represents the model behind the search form about `backend\modules\ezmodules\models\Ezmodule`.
 */
class EzmoduleSearch extends Ezmodule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ezm_id', 'ezm_type', 'ezm_system', 'template_id', 'ezf_id', 'public', 'approved', 'active', 'order_module', 'created_by', 'updated_by', 'ezm_template'], 'integer'],
            [['ezm_name', 'ezm_detail', 'ezm_devby', 'ezm_link', 'ezm_tag', 'ezm_icon', 'icon_base_url', 'ezm_js', 'sitecode', 'ezm_builder', 'share', 'options', 'created_at', 'updated_at'], 'safe'],
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
        $query = Ezmodule::find()->where('active=1');
        //$query->innerJoin('ezmodule_template t', 't.template_id = ezmodule.template_id');
        $query->select(['ezmodule.*', '(select ezf_name from ezform where ezf_id = ezmodule.ezf_id) AS form_name']);
        
        $user_id = Yii::$app->user->id;
        
        if (!Yii::$app->user->can('administrator')) {
            $query->where('ezmodule.created_by=:created_by', [':created_by'=>$user_id]);
        }

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
            'ezm_id' => $this->ezm_id,
            'ezm_type' => $this->ezm_type,
            'ezm_system' => $this->ezm_system,
            'ezmodule.template_id' => $this->template_id,
            'ezmodule.ezf_id' => $this->ezf_id,
            'ezmodule.public' => $this->public,
            'ezmodule.ezm_template' => $this->ezm_template,
            'approved' => $this->approved,
            'ezmodule.active' => $this->active,
            'order_module' => $this->order_module,
            'ezmodule.created_by' => $this->created_by,
            'ezmodule.created_at' => $this->created_at,
            'ezmodule.updated_by' => $this->updated_by,
            'ezmodule.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'ezm_name', $this->ezm_name])
            ->andFilterWhere(['like', 'ezm_detail', $this->ezm_detail])
            ->andFilterWhere(['like', 'ezm_devby', $this->ezm_devby])
            ->andFilterWhere(['like', 'ezm_link', $this->ezm_link])
            ->andFilterWhere(['like', 'ezm_tag', $this->ezm_tag])
            ->andFilterWhere(['like', 'ezm_icon', $this->ezm_icon])
            ->andFilterWhere(['like', 'icon_base_url', $this->icon_base_url])
            ->andFilterWhere(['like', 'ezm_js', $this->ezm_js])
            ->andFilterWhere(['like', 'sitecode', $this->sitecode])
            ->andFilterWhere(['like', 'ezm_builder', $this->ezm_builder])
            ->andFilterWhere(['like', 'ezmodule.share', $this->share])
            ->andFilterWhere(['like', 'ezmodule.options', $this->options]);

        return $dataProvider;
    }
}
