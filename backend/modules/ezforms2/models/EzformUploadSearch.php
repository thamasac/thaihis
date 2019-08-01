<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\EzformUpload;

/**
 * FileUploadSearch represents the model behind the search form about `backend\modules\ezforms\models\FileUpload`.
 */
class EzformUploadSearch extends EzformUpload
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fid', 'tbid', 'file_active', 'ezf_id', 'ezf_field_id', 'created_by'], 'integer'],
            [['target', 'file_name', 'file_name_old', 'created_at'], 'safe'],
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
        $query = EzformUpload::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                //'route' => '/ezforms2/fileinput/grid-update',
            ],
//            'sort' => [
//                'route' => '/ezforms2/fileinput/grid-update',
//            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //file_active = 0 new upload
        //file_active = 1 file confirm
        //file_active = 9 file waiting for approve
        //file_active = -9 file disable
        if($this->mode) $query->where('mode = :mode', [':mode'=>$this->mode]);
        else if($this->mode) $query->where('mode = :mode', [':mode'=>$this->mode]);
        $query->andFilterWhere([
            'fid' => $this->fid,
	        'tbid' => $this->tbid,
            //'file_active' => $this->file_active,
            'ezf_id' => $this->ezf_id,
            'ezf_field_id' => $this->ezf_field_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'file_name', $this->file_name])
	    ->andFilterWhere(['like', 'file_name_old', $this->file_name_old]);
	$query->orderBy('created_at desc');
        return $dataProvider;
    }
}
