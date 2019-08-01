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
class UserSearch extends User
{
    public $firstname;
    public $lastname;
    public $sitecode;
    /**
     * @inheritdoc
     */
    public function rules()
    {
         return [
            'fieldsSafe' => [['id', 'username', 'email', 'registration_ip', 'created_at', 'last_login_at', 'firstname', 'lastname', 'sitecode'], 'safe'],
            'createdDefault' => ['created_at', 'default', 'value' => null],
            'lastloginDefault' => ['last_login_at', 'default', 'value' => null],
        ];
	
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
        $query = $this->finder->getUserQuery();
        $query->joinWith(['profile']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['firstname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['profile.firstname' => SORT_ASC],
            'desc' => ['profile.firstname' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['lastname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['profile.lastname' => SORT_ASC],
            'desc' => ['profile.lastname' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['sitecode'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['profile.sitecode' => SORT_ASC],
            'desc' => ['profile.sitecode' => SORT_DESC],
        ];
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $table_name = $query->modelClass::tableName();

        if ($this->created_at !== null) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', $table_name . '.created_at', $date, $date + 3600 * 24]);
        }

        $query->andFilterWhere(['like', $table_name . '.username', $this->username])
              ->andFilterWhere(['like', $table_name . '.email', $this->email])
              ->andFilterWhere([$table_name . '.id' => $this->id])
                ->andFilterWhere(['like', 'profile.firstname', $this->firstname])
                ->andFilterWhere(['like', 'profile.lastname', $this->lastname])
                ->andFilterWhere(['like', 'profile.sitecode', $this->sitecode])
              ->andFilterWhere([$table_name . 'registration_ip' => $this->registration_ip]);

        return $dataProvider;
    }
}
