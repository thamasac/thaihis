<?php
/*
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use backend\modules\ezforms2\classes\EzfHelper;
use common\modules\user\classes\SiteCodeFunc;
use yii\bootstrap\ActiveForm;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$this->title = Yii::t('chanpan', 'User profile');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>



<div class="col-lg-12">
        <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
        <?= Yii::$app->session->getFlash('success') ?>
        </div>
<?php endif; ?>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-3">
<?= $this->render("_menu") ?>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default"> 
                <div class="panel-heading"><i class='fa fa-history'></i> <?= Yii::t('chanpan', 'User Profile') ?></div>
                <div class="panel-body">

                    <div>
                        <div>
                            <?php
                            $form = ActiveForm::begin([
//                                        'id' => 'frm-profile',
                                        'id'=>$model->formName(),
                                        'layout' => 'horizontal',
                                       // 'enableAjaxValidation' => true,
                                        //'enableClientValidation' => false,
                                        'options' => ['class' => '', 'enctype' => 'multipart/form-data'],
                                        'fieldConfig' => [
                                            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-2 col-lg-9\">{error}\n{hint}</div>",
                                            'labelOptions' => ['class' => 'col-lg-2 control-label'],
                                        ],
                            ]);
                            ?>

                            <?php $form->field($model, 'name') ?>

<?php //$form->field($model, 'public_email') ?>


                            <?php // $form->field($model, 'cid') ?>
                            <?=
                            $form->field($model, 'tel')->widget(\yii\widgets\MaskedInput::className(), [
                                'mask' => '9999999999',
                            ])
                            ?>
                            <?php
                            $url = \yii\helpers\Url::to(['/user/admin/get-sitecode']); //กำหนด URL ที่จะไปโหลดข้อมูล
                            ?>
                            <?php
                            if (!empty($modelFields)) {
                                foreach ($modelFields as $key => $value) {
                                    if (empty($value["input_data"])) {
                                        if ($value['table_varname'] == 'sitecode') {
                                            // Skip for portal
                                            if(\cpn\chanpan\classes\utils\CNDomain::isPortal()){
                                                continue;
                                            }
                                            if (Yii::$app->user->can('administrator')) {
                                                echo SiteCodeFunc::getSiteCode($form, $model);
                                            }else {
                                                $pendingSiteQ = (new \yii\db\Query())->select(['site_detail','target_site'])
                                                    ->from('zdata_site_request')
                                                    ->innerJoin("zdata_sitecode",'zdata_site_request.target_site=zdata_sitecode.site_name')
                                                    ->where(['approve_result'=> ['',null,'0'], 'zdata_site_request.user_create'=>Yii::$app->user->id])
                                                    ->orderBy(['zdata_site_request.create_date'=>'DESC'])->one();
                                                echo SiteCodeFunc::getSiteCode($form, $model, true);
                                                $pendingSiteCode = $pendingSiteQ['target_site'];
                                                $pendingSiteDetail = $pendingSiteQ['site_detail'];
                                                echo "<span class='col-lg-8 text-right'><b>Pending Request:</b> $pendingSiteDetail ($pendingSiteCode)</span>";
                                                backend\modules\ezforms2\classes\EzfStarterWidget::begin();
                                                echo EzfHelper::btn("1530003043092586800")->label('<i class="fa fa-share"></i> Change Site Request')->options(['class' => 'btn btn-info btn-sm col-lg-3', "type" => "button"])->buildBtnAdd();
                                                echo "<div class='clearfix'></div><br>";
                                                backend\modules\ezforms2\classes\EzfStarterWidget::end();
                                            }
                                        }
                                        else if ($value['table_varname'] == 'site_switch') {
                                            //site_switch
                                           
                                           //echo common\modules\user\classes\SiteCodeFunc::get_switch_site($form, $model, '', 'site_switch');
                                        }
                                        else if ($value['table_varname'] != 'certificate') {
                                            echo CoreFunc::generateInput($value, $model, $form, 'table_varname');
                                        }
                                    }
                                }
                            }
                            ?>

                            <?php
                            $roles = [];
                            $rolestr = "";
                            foreach ($model->auth_str as $k => $v) {
                                array_push($roles, \common\modules\user\classes\SiteCodeFunc::getAuthListByName($v)['description']);
                            }
                            $rolestr = join(' , ', $roles);
                            ?> 
                            <div class="form-group field-profile-lastname required">
                                <label class="col-lg-2 control-label" for="profile-lastname"><?= Yii::t('appmenu', 'System Privilege') ?></label>
                                <div class="col-lg-9"><input type="text"  value="<?= $rolestr ?>" class="form-control" readonly></div>
                                <div class="col-sm-offset-3 col-lg-9"><p class="help-block help-block-error "></p>
                                </div>
                                <a tabindex="0"
                                            style='font-size:18px;'   
                                            role="button" 
                                            data-html="true" 
                                            data-toggle="popover" 
                                            data-trigger="focus"
                                            title="<b>Info System Privilege</b>" 
                                            data-content="<?= isset(\Yii::$app->params['system_privilege'])?\Yii::$app->params['system_privilege']:''?>"><i class="fa fa-info-circle"></i></a>
                                            <?php richardfan\widget\JSRegister::begin();?>
                                            <script>
                                                $(function(){
                                                    // Enables popover
                                                    $("[data-toggle=popover]").popover({ html : true,placement: "top" });
                                                });
                                            </script>
                                            <?php richardfan\widget\JSRegister::end();?>
                                            <?php \appxq\sdii\widgets\CSSRegister::begin();?>
                                        <style>
                                           .popover{
                                                width:400px;
                                                height:300px;  
                                                max-width:400px;
                                            }
                                        </style>
                                        <?php \appxq\sdii\widgets\CSSRegister::end();?>
                            </div>

                            <?php
                            echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
                                'url' => ['/core/file-storage/avatar-upload']
                            ])
                            ?>
                            <div id="switch-site"></div>
                            <div id="line-notify-1546924066006893700" style=""></div>
<?= $form->field($model, 'allow_assign')->checkbox()->label(Yii::t('chanpan', 'Allow other research projects to add your account to the project.')); ?>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-9">
<?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-primary btn-lg']) ?><br>
                                </div>
                            </div>


<?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>





<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    init_switch_site = function(){
        let url = '<?= \yii\helpers\Url::to(['/site/switch-site'])?>';
        $.get(url, function(data){
           $('#switch-site').html(data); 
        });
    }
    
    $('form#<?= $model->formName()?>').on('beforeSubmit', function(e) {
        var $form = $(this);
        console.warn('POST');
        $.post(
            $form.attr('action'), //serialize Yii2 form
            $form.serialize()
        ).done(function(result) {
            console.log(result);
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
        }).fail(function() {
            <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });

$.get('/line/default/ajax?title=Line+Notification&reloadDiv=line-notify-1546924066006893700',function(data){
    $('#line-notify-1546924066006893700').html(data);
});

</script>
<?php richardfan\widget\JSRegister::end(); ?> 

