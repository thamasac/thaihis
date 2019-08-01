<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/7/2018
 * Time: 4:51 PM
 */

namespace backend\modules\api\v1\models;
use yii\db\ActiveRecord;

/**
 * Class for infomated api
 * Class ApplicationInfo
 * @package backend\modules\api\v1\models
 */

class  DownloadLinkModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'temporary_download_link';
    }
}