<?php

namespace backend\modules\line;


use Yii;
/**
 * line module definition class
 */
class Module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\line\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['line'])) {
            Yii::$app->i18n->translations['line'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/line/messages'
            ];
        }
        // custom initialization code goes here
    }

}
