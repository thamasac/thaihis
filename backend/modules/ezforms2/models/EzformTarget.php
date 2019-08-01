<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_target".
 *
 * @property string $ezf_id
 * @property string $data_id
 * @property string $target_id
 * @property integer $ptid
 * @property string $xsourcex
 * @property integer $rstat
 * @property string $comp_id
 * @property string $user_create
 * @property integer $user_update
 * @property string $create_date
 * @property string $update_date
 *
 * @property Ezform $ezf
 */
class EzformTarget extends \yii\db\ActiveRecord
{
    public $ezf_name;
    public $ezf_detail;
    public $sitename;
    public $userby;
    
    public $co_dev;
    public $assign;
    public $public_listview;
    public $public_edit;
    public $public_delete;
    public $ezf_table;
    public $category_id;

    /**
     * @inheritdoc
     * 'ezform.co_dev',
            'ezform.assign',
            'ezform.public_listview',
            'ezform.public_edit',
            'ezform.public_delete',
     */
    public static function tableName()
    {
	return 'ezform_target';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            //[['ezf_id', 'data_id', 'target_id'], 'required'],
            [['ezf_id', 'data_id', 'target_id', 'ptid', 'rstat', 'comp_id', 'user_create', 'user_update'], 'integer'],
            [['ezf_name','ezf_detail','sitename','userby', 'create_date', 'update_date'], 'safe'],
            [['xsourcex'], 'string', 'max' => 20],
            //[['ezf_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ezform::className(), 'targetAttribute' => ['ezf_id' => 'ezf_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezf_id' => Yii::t('ezform', 'EzForm'),
	    'data_id' => Yii::t('ezform', 'Data ID'),
	    'target_id' => Yii::t('ezform', 'Target'),
	    'ptid' => Yii::t('ezform', 'Ptid'),
	    'xsourcex' => Yii::t('ezform', 'Site Code'),
	    'rstat' => Yii::t('ezform', 'Status'),
	    'comp_id' => Yii::t('ezform', 'Comp ID'),
	    'user_create' => Yii::t('ezform', 'Created By'),
	    'user_update' => Yii::t('ezform', 'Updated By'),
	    'create_date' => Yii::t('ezform', 'Created At'),
	    'update_date' => Yii::t('ezform', 'Updated At'),
            'ezf_name' => Yii::t('ezform', 'EzForm'),
            'ezf_detail' => Yii::t('ezform', 'Display'),
            'sitename' => Yii::t('ezform', 'Site Code'),
            'userby' => Yii::t('ezform', 'Recorded By'),
	];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEzf()
    {
	return $this->hasOne(Ezform::className(), ['ezf_id' => 'ezf_id']);
    }
}
