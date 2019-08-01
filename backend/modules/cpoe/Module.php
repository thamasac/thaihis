<?php

namespace backend\modules\cpoe;

use Yii;

/**
 * register module definition class
 */
class Module extends \yii\base\Module {

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
    }

}
