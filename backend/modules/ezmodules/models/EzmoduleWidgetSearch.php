<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleWidget;

/**
 * EzmoduleWidgetSearch represents the model behind the search form about `backend\modules\ezmodules\models\EzmoduleWidget`.
 */
class EzmoduleWidgetSearch extends EzmoduleWidget
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['widget_id', 'enable', 'ezm_id', 'ezf_id', 'created_by', 'updated_by', 'widget_attribute'], 'integer'],
            [['widget_name', 'widget_varname', 'widget_render', 'widget_type', 'widget_detail', 'widget_example', 'options', 'created_at', 'updated_at'], 'safe'],
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
        $query = EzmoduleWidget::find();

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
            'widget_id' => $this->widget_id,
            'enable' => $this->enable,
            'ezm_id' => $this->ezm_id,
            'ezf_id' => $this->ezf_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'widget_attribute' => $this->widget_attribute,
        ]);

        $query->andFilterWhere(['like', 'widget_name', $this->widget_name])
            ->andFilterWhere(['like', 'widget_varname', $this->widget_varname])
            ->andFilterWhere(['like', 'widget_render', $this->widget_render])    
            ->andFilterWhere(['like', 'widget_type', $this->widget_type])
            ->andFilterWhere(['like', 'widget_detail', $this->widget_detail])
            ->andFilterWhere(['like', 'widget_example', $this->widget_example])
            ->andFilterWhere(['like', 'options', $this->options]);

        return $dataProvider;
    }
    
    public function searchList($params)
    {
        $query = EzmoduleWidget::find();
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'widget_id' => $this->widget_id,
            'enable' => $this->enable,
            'ezm_id' => $this->ezm_id,
            'ezf_id' => $this->ezf_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'widget_attribute' => $this->widget_attribute,
        ]);

        $query->andFilterWhere(['like', 'widget_name', $this->widget_name])
            ->andFilterWhere(['like', 'widget_varname', $this->widget_varname])
            ->andFilterWhere(['like', 'widget_render', $this->widget_render])    
            ->andFilterWhere(['like', 'widget_type', $this->widget_type])
            ->andFilterWhere(['like', 'widget_detail', $this->widget_detail])
            ->andFilterWhere(['like', 'widget_example', $this->widget_example])
            ->andFilterWhere(['like', 'options', $this->options]);

        $query->orWhere("widget_type='core'");
        
        return $dataProvider;
    }
    
    public function searchListModule($params, $ezm_id)
    {
        $query = EzmoduleWidget::find()->where("enable=1 AND (widget_type='core' OR ezm_id=:ezm_id)", [':ezm_id'=>$ezm_id])->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'widget_id' => $this->widget_id,
            'enable' => $this->enable,
            'ezm_id' => $this->ezm_id,
            'ezf_id' => $this->ezf_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'widget_attribute' => $this->widget_attribute,
        ]);

        $query->andFilterWhere(['like', 'widget_name', $this->widget_name])
            ->andFilterWhere(['like', 'widget_varname', $this->widget_varname])
            ->andFilterWhere(['like', 'widget_render', $this->widget_render])    
            ->andFilterWhere(['like', 'widget_type', $this->widget_type])
            ->andFilterWhere(['like', 'widget_detail', $this->widget_detail])
            ->andFilterWhere(['like', 'widget_example', $this->widget_example])
            ->andFilterWhere(['like', 'options', $this->options]);

        return $dataProvider;
    }
}
