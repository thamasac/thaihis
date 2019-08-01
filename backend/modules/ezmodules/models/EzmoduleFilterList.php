<?php

namespace backend\modules\ezmodules\models;

use Yii;

/**
 * This is the model class for table "ezmodule_filter_list".
 *
 * @property integer $list_id
 * @property integer $ezm_id
 * @property integer $filter_id
 * @property integer $dataid
 */
class EzmoduleFilterList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_filter_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['list_id', 'filter_id', 'dataid'], 'required'],
            [['list_id', 'ezm_id', 'filter_id', 'dataid'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'list_id' => Yii::t('ezmodule', 'List ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Ezm ID'),
	    'filter_id' => Yii::t('ezmodule', 'Filter ID'),
	    'dataid' => Yii::t('ezmodule', 'Dataid'),
	];
    }
}
