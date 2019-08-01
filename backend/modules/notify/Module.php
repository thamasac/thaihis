<?php

namespace backend\modules\notify;

use Yii;

/**
 * notify module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\notify\controllers';

     /**
     * @inheritdoc
     */
    public function init() {
            parent::init();
            if (!isset(Yii::$app->i18n->translations['noti'])) {
                    Yii::$app->i18n->translations['noti'] = [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'sourceLanguage' => 'en',
                            'basePath' => '@backend/modules/notify/messages'
                    ];
            }
    }
}
