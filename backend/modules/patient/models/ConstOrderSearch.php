<?php

namespace backend\modules\patient\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\patient\models\ConstOrder;

/**
 * ConstOrderSearch represents the model behind the search form about `backend\modules\patient\models\ConstOrder`.
 */
class ConstOrderSearch extends ConstOrder {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['order_code', 'order_name', 'group_code', 'group_type', 'fin_item_code', 'sks_code', 'order_status', 'order_group_name'], 'safe'],
            [['full_price'], 'number'],
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
    public function search($params) {
        $query = ConstOrder::find();

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
            'full_price' => $this->full_price,
        ]);

        $query->andFilterWhere(['like', 'order_code', $this->order_code])
                ->andFilterWhere(['like', 'order_name', $this->order_name])
                ->andFilterWhere(['like', 'group_code', $this->group_code])
                ->andFilterWhere(['like', 'group_type', $this->group_type])
                ->andFilterWhere(['like', 'fin_item_code', $this->fin_item_code])
                ->andFilterWhere(['like', 'sks_code', $this->sks_code])
                ->andFilterWhere(['like', 'order_status', $this->order_status]);

        return $dataProvider;
    }

    public function searchOrderList($params, $userposition) {
        $addonWhere = [];
        if ($userposition == '2') {
            $addonWhere = ['<>', 'group_type', 'S'];
        }
        $query = ConstOrder::find()
                ->select(["order_group_name", "const_order.order_code", "order_name", "order_type_name", "full_price", "group_type", "ezf_id", "external_flag"])
                ->innerJoin('const_order_type', 'order_type_code=group_type')
                ->innerJoin('const_order_group', 'group_code=order_group_code')
//                ->leftJoin('const_order_dept', 'const_order.order_code=const_order_dept.order_code')
                ->andWhere(['order_status' => '1'])
                ->andWhere(['<>', 'group_type', 'D'])
                ->andWhere($addonWhere)
//                ->orderBy('order_group_orderby ASC,c_order DESC,dept_code DESC,order_group_name,order_name');
                ->orderBy('c_order DESC,order_group_name,order_name');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['attributes' => ['fullname', 'ezf_name', 'ezf_detail', 'created_at']],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        $query->andFilterWhere([
//            'ezf_id' => $this->ezf_id,
//            'created_by' => $this->created_by,
//        ]);

        $query->andFilterWhere(['like', "CONCAT(order_group_name,' ',const_order.order_code,' ',order_name)", $this->order_name]);
        $query->andFilterWhere(['like', 'group_type', $this->group_type]);

        return $dataProvider;
    }

}
