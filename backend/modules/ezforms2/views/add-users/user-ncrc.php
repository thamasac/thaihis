<?php
$this->title = Yii::t('chanpan', 'New user nCRC');
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap\ActiveForm;
?>
<?= $this->render('_menu') ?>
 
    <div class="panel panel-primary">
    <div class="panel-heading"><?= yii\helpers\Html::encode($this->title); ?></div>
    <div class="panel-body">
        <div class="row">
            <div>
                <?php ActiveForm::begin(); ?>
                <div class="col-md-3">
                    <?php
                        echo \cpn\chanpan\classes\CNUser::getUserSelect2SingleAjaxBySite("user_id", Yii::t('chanpan', 'User'), "", "ncrc");
                    ?>
                </div>
                <div class="col-md-3">
                    <?php
                         echo \common\modules\user\classes\CNSitecode::getSiteCodeSelect2SingleAjaxBySite("site_id", Yii::t('chanpan', 'Sitecode'), "")
                    ?>                    
                </div>
                <div class="col-md-3">
                    <?php 
                        echo \common\modules\user\classes\CNDepartment::getDepartmentFormNotModel("department_id", Yii::t("chanpan",'Department'), '');
                    ?>
                </div>
                <div class="col-md-3">
                    <br>
                    <div style="margin-top:5px;">
                        <?= yii\bootstrap\Html::button("<i class='fa fa-plus'></i> ".Yii::t('chanpan', 'Add User'), ['class'=>'btn btn-success'])?>
                    </div>
                </div>
                    
            <?php ActiveForm::end(); ?>
        </div>
        </div>
    </div>
</div>
 