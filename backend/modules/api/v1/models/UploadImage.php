<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 10/27/2017
 * Time: 11:19 AM
 */

namespace backend\modules\api\v1\models;

use common\lib\codeerror\helpers\GenMillisecTime;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadImage extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload($prefix)
    {
        if ($this->validate()) {
            $id = GenMillisecTime::getMillisecTime();
            $this->imageFile->saveAs('fileinput/' . $prefix . '_' . $id .'.' . $this->imageFile->extension);
            return $id;
        } else {
            return false;
        }
    }
}