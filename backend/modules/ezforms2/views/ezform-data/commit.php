<?php

use yii\helpers\Html;
use yii\helpers\Url;

$msgThank = \backend\modules\core\classes\CoreQuery::getOptions('token_thanks');
$msgNew = \backend\modules\core\classes\CoreQuery::getOptions('token_new');
$msgRegister = \backend\modules\core\classes\CoreQuery::getOptions('token_register');
$msgConten = \backend\modules\core\classes\CoreQuery::getOptions('token_content');

$modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
$userProfile = \common\modules\user\models\Profile::findOne($modelEzf->created_by);

$options = appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);

\Yii::$app->view->registerMetaTag([
    'charset' => 'UTF-8',
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1.0'
]);

?>
<?= Html::csrfMetaTags() ?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>

<?php backend\modules\ezforms2\classes\EzfStarterWidget::end(); ?>
<div class="container-fluid" style="margin-top: 15px;margin-bottom: 15px;">
    <?php
    if(isset($options['token']) && $options['token_mar']!=''){
        echo $options['token_mar'];
    } else {
        echo $msgThank->option_value;
    };
    
   ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <?php 
            if($unique==0){
                echo \yii\bootstrap\Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('ezform', $msgNew->option_value), Url::to(['index', 'ezf_id' => $ezf_id, 'token' => $token]), ['class' => 'btn btn-success btn-lg']); 
            }
            echo ' '.\yii\bootstrap\Html::a('<i class="glyphicon glyphicon-user"></i> ' . Yii::t('ezform', $msgRegister->option_value), Url::to(['/user/register']), ['class' => 'btn btn-primary btn-lg']); 
            ?>
          <p><strong><?= Yii::t('ezform', 'Survey owner:')?></strong> <?=$userProfile->firstname?> <?=$userProfile->lastname?> <strong><?= Yii::t('ezform', 'E-mail:')?></strong> <a href="mailto:#"><?=$userProfile->public_email?></a></p>
            <p><h3><?=Yii::$app->params['powered_by']?></h3></p> 
        </div>
    </div>
    <?=$msgConten->option_value?>
</div>


