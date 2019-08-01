<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use appxq\sdii\utils\SDUtility;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ezform_autonum".
 *
 * @property integer $id
 * @property string $label
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property integer $digit
 * @property string $prefix
 * @property integer $count
 * @property string $suffix
 * @property integer $bysite
 * @property string $count_site
 * @property integer $per_time
 * @property integer $per_day
 * @property integer $status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $type
 */
class EzformAutonum extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ezform_autonum';
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'count',
                ],
                'value' => function ($event) {
                    $userProfile = (isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : \common\modules\user\models\User::findOne(['id' => '1'])->profile);
                    $model = $event->sender;
                    
                    if($model->bysite==1){
                        $countSite = SDUtility::string2Array($model->count_site);
                        $model->count = isset($countSite[$userProfile->sitecode])?$countSite[$userProfile->sitecode]:1;
                    }
                    return $model->count;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'count_site',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'count_site',
                ],
                'value' => function ($event) {
                    $userProfile = (isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : \common\modules\user\models\User::findOne(['id' => '1'])->profile);
                    $model = $event->sender;
                    
                    if($model->bysite==1){
                        $countSite = SDUtility::string2Array($model->count_site);
                        $countSite[$userProfile->sitecode] = $model->count;
                        $model->count_site = SDUtility::array2String($countSite);
                    }
                    
                    return $model->count_site;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'label'], 'required'],
            [['id', 'ezf_id', 'ezf_field_id', 'digit', 'count', 'bysite', 'status', 'created_by', 'updated_by', 'per_time', 'per_day'], 'integer'],
            [['count_site'], 'string'],
            [['created_at', 'updated_at', 'type'], 'safe'],
            [['label'], 'string', 'max' => 100],
            [['prefix', 'suffix'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('ezform', 'ID'),
            'type' => Yii::t('ezform', 'Type'),
            'label' => Yii::t('ezform', 'Name'),
            'ezf_id' => Yii::t('ezform', 'Ezf ID'),
            'ezf_field_id' => Yii::t('ezform', 'Ezf Field ID'),
            'digit' => Yii::t('ezform', 'Number of digits'),
            'prefix' => Yii::t('ezform', 'Prefix'),
            'count' => Yii::t('ezform', 'Initial Value'),
            'suffix' => Yii::t('ezform', 'Suffix'),
            'bysite' => Yii::t('ezform', 'By Site'),
            'count_site' => Yii::t('ezform', 'Count Site'),
            'per_time' => Yii::t('ezform', 'Increment'),
            'per_day' => Yii::t('ezform', 'Per Day'),
            'status' => Yii::t('ezform', 'Enable'),
            'created_by' => Yii::t('ezform', 'Created By'),
            'created_at' => Yii::t('ezform', 'Created At'),
            'updated_by' => Yii::t('ezform', 'Updated By'),
            'updated_at' => Yii::t('ezform', 'Updated At'),
        ];
    }

}
