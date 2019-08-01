<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/7/2018
 * Time: 4:51 PM
 */

namespace backend\modules\api\v1\models;

use backend\modules\api\v1\classes\LogStash;
use yii\db\ActiveRecord;

class  EzFormOriginModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'ezform_origin_info';
    }

    /**
     * @param $ezf_id
     * @param $applicationInfo ApplicationInfo
     * @param $arr
     */
    public function setModel($ezf_id, $applicationInfo, $arr)
    {
        try {
            $this->setAttribute("id", GenMillisecTime::getMillisecTime());
            $this->setAttribute("data_id", $arr['id']);
            $this->setAttribute("update_user", $arr['user_update']);
            $this->setAttribute("update_date", $arr['update_date']);
            $this->setAttribute("sitecode", $arr['hsitecode']);
            $this->setAttribute("data_id", $arr['id']);
            $this->setAttribute("form_rstat", $arr['rstat']);
            $this->setAttribute("ezf_id", $ezf_id);
            $this->setAttribute("application", $applicationInfo->application);
            $this->setAttribute("platform", $applicationInfo->platform);
            $this->setAttribute("version", $applicationInfo->version);
            $this->setAttribute("device_id", $applicationInfo->deviceId);
            LogStash::Log($arr['user_update'],"setModel",var_export($this->attributes,true),"",$applicationInfo);

        } catch (\Exception $e) {
            LogStash::ErrorEx($applicationInfo->user_id, "EzFormOriginModel", $e, "error#setModel", "EzFormAPI");
        }
    }
}