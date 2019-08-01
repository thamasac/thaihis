<?php

namespace backend\modules\ezmodules;

use Yii;

/**
 * ezmodules module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\ezmodules\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['ezmodule'])) {
                Yii::$app->i18n->translations['ezmodule'] = [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'sourceLanguage' => 'en',
                        'basePath' => '@backend/modules/ezmodules/messages'
                ];
        }
    }
}
