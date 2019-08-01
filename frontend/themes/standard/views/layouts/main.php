<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use frontend\assets\AppAsset;
use yii\widgets\Breadcrumbs;
use common\lib\sdii\widgets\SDAlert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="page-header-fixed  page-sidebar-fixed  <?= (isset($_COOKIE['sidebar_toggler']) && $_COOKIE['sidebar_toggler']==1)?'':'page-sidebar-closed';?> ">
    <?php $this->beginBody() ?>

    <?php 
        $moduleID = '';
        $controllerID = '';
        $actionID = '';

        if (isset(Yii::$app->controller->module->id)) {
            $moduleID = Yii::$app->controller->module->id;
        }
        if (isset(Yii::$app->controller->id)) {
            $controllerID = Yii::$app->controller->id;
        }
        if (isset(Yii::$app->controller->action->id)) {
            $actionID = Yii::$app->controller->action->id;
        }
        
        frontend\components\AppComponent::navbarMenu($moduleID, $controllerID, $actionID);
        frontend\components\AppComponent::navbarRightMenu();
        
        NavBar::begin([
	    'brandLabel' => Html::img('img/ncrc.png',['style'=>'width:100px;margin-top:-10px;display:inline-block;']),
	    'brandUrl' => Yii::$app->homeUrl,
	    'innerContainerOptions' => ['class'=>'container'],
	    'options' => [
		'class' => 'page-container navbar navbar-inverse bg-dark navbar-fixed-top',
	    ],
	]);
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav'],
	    'items' => isset(Yii::$app->params['navbar'])?Yii::$app->params['navbar']:[],
	]);
	
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav navbar-right'],
	    'items' => isset(Yii::$app->params['navbarR'])?Yii::$app->params['navbarR']:[],
	]);
	
	echo '<div class="navbar-text pull-right">';
	echo \lajax\languagepicker\widgets\LanguagePicker::widget([
	    'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN,
	    'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
	]);
	echo '</div>';
	NavBar::end();
    ?>
    <div class="container" style="padding-top: 51px;">
        <?php foreach (Yii::$app->session->getAllFlashes() as $message): ?>
        <div class="container" style="padding-top: 15px;">
            <?php
            if(isset($message['body'])){
                \yii\bootstrap\Alert::widget([
                    'body'=>$message['body'],
                    'options'=>$message['options'],
            ]);
                
            }else {
                \yii\bootstrap\Alert::widget([
                        'body'=>$message,
                        'options'=>['class' => 'alert-warning'],
                ]);
            }
            ?>
        </div>
        <?php endforeach; ?>
        
    </div>
    
  <div class="content container" style="margin-top: 15px;">
      <?php echo $content; ?>
  </div>
    
    <?php echo $this->render('//layouts/_footer'); ?>
    
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>