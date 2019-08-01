<?php

namespace backend\modules\pis;

use Yii;

/**
 * modules module definition class
 */
class module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\pis\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['patient'])) {
            Yii::$app->i18n->translations['patient'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/patient/messages'
            ];
        }

        if (!isset(Yii::$app->i18n->translations['ezform'])) {
            Yii::$app->i18n->translations['ezform'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/ezforms2/messages'
            ];
        }
        // custom initialization code goes here
    }

}
