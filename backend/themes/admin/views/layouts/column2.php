<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
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

    <?php NavBar::begin([
	    'brandLabel' => 'My Company',
	    'brandUrl' => Yii::$app->homeUrl,
	    'innerContainerOptions' => ['class'=>'container-fluid'],
	    'options' => [
		'class' => 'page-container navbar navbar-inverse navbar-fixed-top',
	    ],
	]);
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav'],
	    'items' => Yii::$app->params['navbar'],
	]);
	
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav navbar-right'],
	    'items' => Yii::$app->params['navbarR'],
	]);
	NavBar::end();
    ?>
    
    <?= $this->render('//layouts/_page-sidebar') ?>
		
    <section class="page-container page-content" role="main">
	<div class="sdbox">
	    <div class="page-column column2">
		<?= Breadcrumbs::widget([
		    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>
		
		<?php foreach (Yii::$app->session->getAllFlashes() as $message): ?>
			<?= \yii\bootstrap\Alert::widget([
				'body'=>$message['body'],
				'options'=>$message['options'],
			])?>
		<?php endforeach; ?>
			
		<?php echo $content; ?>

		<?php echo $this->render('//layouts/_rightside'); ?>

		<?php echo $this->render('//layouts/_footer'); ?>
	    </div>
	</div>
    </section>
    
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>