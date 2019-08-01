<?php

namespace backend\modules\log\models;

use Yii;

/**
 * This is the model class for table "dynamic_db".
 *
 * @property int $id
 * @property string $url
 * @property string $url_change
 * @property int $data_id
 * @property string $config_db
 * @property string $proj_name
 * @property string $dbname
 * @property string $create_at
 * @property string $project_template
 * @property string $user_create
 * @property int $tctr_id
 * @property string $host
 * @property string $pi_name
 * @property string $aconym
 * @property int $rstat
 * @property int $approved
 */
class DynamicDb extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dynamic_db';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'url_change', 'data_id'], 'required'],
            [['data_id', 'user_create', 'tctr_id', 'rstat', 'approved'], 'integer'],
            [['config_db'], 'string'],
            [['create_at'], 'safe'],
            [['url', 'url_change', 'proj_name', 'dbname', 'project_template', 'host', 'pi_name', 'aconym'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'url_change' => Yii::t('app', 'Url Change'),
            'data_id' => Yii::t('app', 'Data ID'),
            'config_db' => Yii::t('app', 'Config Db'),
            'proj_name' => Yii::t('app', 'Proj Name'),
            'dbname' => Yii::t('app', 'Dbname'),
            'create_at' => Yii::t('app', 'Create At'),
            'project_template' => Yii::t('app', 'Project Template'),
            'user_create' => Yii::t('app', 'User Create'),
            'tctr_id' => Yii::t('app', 'Tctr ID'),
            'host' => Yii::t('app', 'Host'),
            'pi_name' => Yii::t('app', 'Pi Name'),
            'aconym' => Yii::t('app', 'Aconym'),
            'rstat' => Yii::t('app', 'Rstat'),
            'approved' => Yii::t('app', 'Approved'),
        ];
    }
}
