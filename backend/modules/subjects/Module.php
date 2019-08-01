<?php

namespace backend\modules\subjects;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'backend\modules\subjects\controllers';

    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['subjects'])) {
            Yii::$app->i18n->translations['subjects'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/subjects/messages'
            ];
        }
        // custom initialization code goes here
    }

    public static $formsId = [
        'research_ezf_id' => '1560330171055404200',
        'budget_pms_ezf_id' => '1561103574014768900',
    ];
    public static $formsTable = [
        'research_table' => 'zdata_create_research',
        'budget_pms_ezf_id' => 'zdata_budget_pms',
    ];

}
