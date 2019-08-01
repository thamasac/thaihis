<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/7/2018
 * Time: 4:51 PM
 */

namespace backend\modules\api\v1\models;

/**
 * Class for infomated api
 * Class ApplicationInfo
 * @package backend\modules\api\v1\models
 */
class ApplicationInfo
{
    public $platform;
    public $application;
    public $version;
    public $deviceId;
    public $user_id;

    function __construct($u, $p, $a, $v, $d)
    {
        $this->platform = $p;
        $this->application = $a;
        $this->version = $v;
        $this->deviceId = $d;
        $this->user_id = $u;
    }
}