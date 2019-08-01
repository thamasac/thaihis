<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformFieldsLib;

/**
 * EzformFieldsLibSearch represents the model behind the search form about `backend\modules\ezfieldlib\models\EzformFieldsLib`.
 */
class EzformFieldsLibSearch extends EzformFieldsLib {

    public $ezf_name, $lib_group_name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['field_lib_id', 'ezf_field_id', 'ezf_id', 'field_lib_group', 'field_lib_approved', 'field_lib_share', 'field_lib_status', 'created_by', 'updated_by'], 'integer'],
            [['ezf_field_name', 'ezf_name', 'lib_group_name', 'field_lib_name', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $mode = 'grid') {
        $user_id = Yii::$app->user->identity->profile->user_id;
        $query = EzformFieldsLib::find();

        $query->select(['elib.field_lib_group', 'elib.field_lib_share', 'elib.field_lib_id', 'elib.ezf_id',
                    'elib.field_lib_name', "CONCAT(elib.ezf_field_label,' (',elib.ezf_field_name,')') AS ezf_field_name",
                    'ezf.ezf_name', 'elib.field_lib_approved', 'eflg.lib_group_name', 'elib.ezf_field_id', 'ezf.ezf_version',
                    'elib.updated_at', 'elib.field_lib_status', 'elib.ezf_field_type'
                ])
                ->alias('elib')
                ->innerJoin('ezform ezf', 'ezf.ezf_id=elib.ezf_id')
//                ->innerJoin('ezform_fields ezff', 'ezff.ezf_field_id=elib.ezf_field_id')
                ->leftJoin('ezform_fields_lib_group eflg', 'eflg.lib_group_id=elib.field_lib_group');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);
        if ($mode <> 'modal') {
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
                return $dataProvider;
            }
        }

        $query->andFilterWhere([
//            'field_lib_id' => $this->field_lib_id,
            'field_lib_group' => $this->lib_group_name,
//            'field_lib_share' => $this->field_lib_share,
            'field_lib_approved' => $this->field_lib_approved,
        ]);

        $query->andFilterWhere(['like', 'elib.ezf_field_name', $this->ezf_field_name])
                ->andFilterWhere(['like', 'ezf.ezf_name', $this->ezf_name]);

        if ($mode == 'modal') {
            $query->andFilterWhere(['like', "CONCAT(field_lib_name,IFNULL(lib_group_name,''))", $this->field_lib_name])
                    ->andWhere(['elib.field_lib_status' => 1]);

            if (is_array($this->field_lib_share)) {
                $sharedFilter = "((field_lib_share=" . implode(") OR (field_lib_share=", $this->field_lib_share) . "))";
                $sharedFilter = str_replace("field_lib_share=2", "field_lib_share=2 AND (elib.created_by='{$user_id}')", $sharedFilter);
                $query->andWhere($sharedFilter);
            } else {
                $query->andWhere(['elib.created_by' => $user_id]);
            }
        } else {
            $query->andFilterWhere(['like', 'field_lib_name', $this->field_lib_name])
                    ->andFilterWhere(['elib.created_by' => $user_id, 'field_lib_share' => $this->field_lib_share,])
                    ->andFilterWhere(['field_lib_status' => $this->field_lib_status]);
        }
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        $dataProvider->sort->attributes['ezf_name'] = [
            'asc' => ['ezf.ezf_name' => SORT_ASC],
            'desc' => ['ezf.ezf_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['ezf_field_name'] = [
            'asc' => ['elib.ezf_field_name' => SORT_ASC],
            'desc' => ['elib.ezf_field_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lib_group_name'] = [
            'asc' => ['eflg.lib_group_name' => SORT_ASC],
            'desc' => ['eflg.lib_group_name' => SORT_DESC],
        ];

        return $dataProvider;
    }

    public function searchName($q) {
        $data = EzformFieldsLib::find()
                ->andFilterWhere(['like', 'field_lib_name', $q])
                ->one();

        return $data;
    }

    /**
     * 
     * @param type $ezf_field_id
     * @param type $user_id
     * @return type
     */
    public static function getLibrary($ezf_field_id, $user_id) {
        $query = EzformFieldsLib::find();

        return $query->select(['elib.field_lib_group', 'elib.field_lib_share', 'elib.field_lib_id', 'elib.ezf_id',
                            'elib.field_lib_name', "CONCAT(elib.ezf_field_label,' (',elib.ezf_field_name,')') AS ezf_field_name",
                            'elib.field_lib_approved', 'eflg.lib_group_name', 'elib.ezf_field_id',
                            'elib.updated_at', 'elib.field_lib_status'])
                        ->alias('elib')
                        ->leftJoin('ezform_fields_lib_group eflg', 'eflg.lib_group_id=elib.field_lib_group')
                        ->asArray()
                        ->where(['elib.ezf_field_id' => $ezf_field_id, 'elib.created_by' => $user_id])
                        ->one();
    }

}
