<?php

namespace backend\modules\thaihis;

use Yii;

/**
 * register module definition class
 */
class Module extends \yii\base\Module {    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\thaihis\controllers';

    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['thaihis'])) {
            Yii::$app->i18n->translations['thaihis'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/thaihis/messages'
            ];
        }

        if (!isset(Yii::$app->i18n->translations['ezform'])) {
            Yii::$app->i18n->translations['ezform'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/ezforms2/messages'
            ];
        }
    }

}
