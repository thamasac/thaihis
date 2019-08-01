<?php

namespace backend\modules\manageproject\models;

use Yii;

/**
 * This is the model class for table "{{%zdata_create_project}}".
 *
 * @property int $id ไอดี
 * @property int $ptid ไอดีเป้าหมายแรก
 * @property string $sitecode หน่วยงานแรก
 * @property string $ptcode รหัสเป้าหมายหน่วยงานแรก
 * @property string $ptcodefull ptcode+sitecode
 * @property int $target ไอดีของเป้าหมาย
 * @property string $hptcode รหัสเป้าหมายของหน่วยงานนั้น
 * @property string $hsitecode หน่วยงานที่บันทึก(xsourcex)
 * @property string $xsourcex หน่วยงานที่บันทึก
 * @property string $xdepartmentx ฝ่ายงานที่บันทึก
 * @property string $sys_lat
 * @property string $sys_lng
 * @property string $error
 * @property int $rstat สถานะ 
 * @property int $user_create
 * @property string $create_date
 * @property int $user_update
 * @property string $update_date
 * @property string $ezf_version
 * @property string $studydesign
 * @property string $tctrno
 * @property string $projectname
 * @property string $projectacronym
 * @property string $projecticon
 * @property string $projurl
 * @property string $projdomain
 * @property string $sharing
 * @property string $useTemplate
 * @property string $briefsummary
 * @property string $detail
 * @property string $id_tctr
 * @property string $pi_name
 * @property string $status_form
 */
class CreateProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%zdata_create_project}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [       
            [[
                'useTemplate', 
                'studydesign',
                'sharing',
                'tctrno',
                'projectacronym',
                'projurl',
                'projdomain',
                'id_tctr',
                'pi_name',
                'status_form',
                'projecticon'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('chanpan', 'ID'),
            'ptid' => Yii::t('chanpan', 'Ptid'),
            'sitecode' => Yii::t('chanpan', 'Sitecode'),
            'ptcode' => Yii::t('chanpan', 'Ptcode'),
            'ptcodefull' => Yii::t('chanpan', 'Ptcodefull'),
            'target' => Yii::t('chanpan', 'Target'),
            'hptcode' => Yii::t('chanpan', 'Hptcode'),
            'hsitecode' => Yii::t('chanpan', 'Hsitecode'),
            'xsourcex' => Yii::t('chanpan', 'Xsourcex'),
            'xdepartmentx' => Yii::t('chanpan', 'Xdepartmentx'),
            'sys_lat' => Yii::t('chanpan', 'Sys Lat'),
            'sys_lng' => Yii::t('chanpan', 'Sys Lng'),
            'error' => Yii::t('chanpan', 'Error'),
            'rstat' => Yii::t('chanpan', 'Rstat'),
            'user_create' => Yii::t('chanpan', 'User Create'),
            'create_date' => Yii::t('chanpan', 'Create Date'),
            'user_update' => Yii::t('chanpan', 'User Update'),
            'update_date' => Yii::t('chanpan', 'Update Date'),
            'ezf_version' => Yii::t('chanpan', 'Ezf Version'),
            'studydesign' => Yii::t('chanpan', 'Studydesign'),
            'tctrno' => Yii::t('chanpan', 'Tctrno'),
            'projectname' => Yii::t('chanpan', 'Projectname'),
            'projectacronym' => Yii::t('chanpan', 'Projectacronym'),
            'projecticon' => Yii::t('chanpan', 'Projecticon'),
            'projurl' => Yii::t('chanpan', 'Projurl'),
            'projdomain' => Yii::t('chanpan', 'Projdomain'),
            'sharing' => Yii::t('chanpan', 'Sharing'),
            'useTemplate' => Yii::t('chanpan', 'Use Template'),
            'briefsummary' => Yii::t('chanpan', 'Briefsummary'),
            'detail' => Yii::t('chanpan', 'Detail'),
            'id_tctr' => Yii::t('chanpan', 'Id Tctr'),
            'pi_name' => Yii::t('chanpan', 'Pi Name'),
            'status_form' => Yii::t('chanpan', 'Status Form'),
        ];
    }
}
