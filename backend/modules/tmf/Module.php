<?php

namespace backend\modules\tmf;
use Yii;

/**
 * tmf module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\tmf\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

       if (!isset(Yii::$app->i18n->translations['tmf'])) {
            Yii::$app->i18n->translations['tmf'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/tmf/messages'
            ];
        }
    }
}
