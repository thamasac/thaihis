<?php

namespace backend\modules\ce;

/**
 * ce module definition class
 */
class Module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\ce\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (!isset(\Yii::$app->i18n->translations['ec'])) {
            \Yii::$app->i18n->translations['ec'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/ce/messages'
            ];
        }
    }

}
