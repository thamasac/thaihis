<?php

namespace backend\modules\core;

use Yii;

class Module extends \yii\base\Module {

	public $controllerNamespace = 'backend\modules\core\controllers';
	//sdii core config
	public $noParentTag = array('post_tag'); // use  Yii::$app->controller->module->noParentTag
	public $hasParentPost = array('page'); // use  Yii::$app->controller->module->hasParentPost
	public $postStatusList = array('publish' => 'Publish', 'private' => 'Private', 'draft' => 'Draft'); //Yii::$app->controller->module->postStatusList
	public $formatPostList = array('Standard', 'Aside', 'Image', 'Link', 'Quote'); //Yii::$app->controller->module->formatPostList
	public $templateList = array('default'=>'Default Template', 'contrbutor'=>'Contrbutor Page', 'fullwidth'=>'Full Width Page'); //Yii::$app->controller->module->templateList

	public function init() {
		parent::init();
		if (!isset(Yii::$app->i18n->translations['core'])) {
			Yii::$app->i18n->translations['core'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'sourceLanguage' => 'en',
				'basePath' => '@backend/modules/core/messages'
			];
		}
	}

}
