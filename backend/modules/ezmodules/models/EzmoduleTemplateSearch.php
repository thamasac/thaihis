<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezmodules\models\EzmoduleTemplate;

/**
 * EzmoduleTemplateSearch represents the model behind the search form about `backend\modules\ezmodules\models\EzmoduleTemplate`.
 */
class EzmoduleTemplateSearch extends EzmoduleTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'template_system', 'public', 'created_by', 'updated_by'], 'integer'],
            [['template_name', 'template_html', 'template_js', 'template_js', 'sitecode', 'created_at', 'updated_at'], 'safe'],
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
        $query = EzmoduleTemplate::find()->orderBy('template_id');
        $user_id = Yii::$app->user->id;
        
        if (!Yii::$app->user->can('administrator')) {
            $query->where('created_by=:created_by', [':created_by'=>$user_id]);
        }
        
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
            'template_id' => $this->template_id,
            'template_system' => $this->template_system,
            'public' => $this->public,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'template_name', $this->template_name])
            ->andFilterWhere(['like', 'template_html', $this->template_html])
            ->andFilterWhere(['like', 'template_js', $this->template_js])
            ->andFilterWhere(['like', 'template_css', $this->template_css])    
            ->andFilterWhere(['like', 'sitecode', $this->sitecode]);

        return $dataProvider;
    }
}
