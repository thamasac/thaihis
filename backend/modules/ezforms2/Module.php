<?php

namespace backend\modules\ezforms2;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\ezforms2\controllers';

    public function init() {
            parent::init();
            if (!isset(Yii::$app->i18n->translations['ezform'])) {
                    Yii::$app->i18n->translations['ezform'] = [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'sourceLanguage' => 'en',
                            'basePath' => '@backend/modules/ezforms2/messages'
                    ];
            }
    }
}
