<div class="devise-header text-center clearfix">
    <div class="devise-menu <?= ($active == 'login') ? 'active osh-red text-bold' : ''?>">
       <?php 
         if($active == 'login'){
             echo Yii::t('chanpan', 'Sign in');
         }else{
             echo yii\helpers\Html::a(Yii::t('chanpan', 'Login'), ['/user/login'], []);
         }
       ?>
    </div>
    <div class="devise-menu <?= ($active == 'register') ? 'active osh-red text-bold' : ''?>">
        <?php 
         if($active == 'register'){
             echo Yii::t('chanpan', 'Apply for a new account');
         }else{
             echo yii\helpers\Html::a(Yii::t('chanpan', 'Apply for a new account'), ['/user/register'], []);
         }
       ?>
    </div>
</div>
