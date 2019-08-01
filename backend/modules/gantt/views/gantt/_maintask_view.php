<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\gantt\classes\GanttQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$user_id = Yii::$app->user->id;
$ownProject = false;
$projectForm = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id) ;
$ownProject = GanttQuery::checkOwnData($projectForm, $model['id']);
if (!$ownProject) {
    $dataCreate = cpn\chanpan\classes\utils\CNProject::getMyProject();
    if ($dataCreate['data_create']['user_create'] == $user_id) {
        $ownProject = true;
    }
}

$image_field = Html::img(Yii::getAlias('@storageUrl/images/pms_icon.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 40px;']);
if(!\backend\modules\gantt\classes\GanttQuery::isUrlExist(Yii::getAlias('@storageUrl/images/pms_icon.png'))){
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/noimg.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 40px;']);
}

if (isset($model['icon']) && $model['icon'] != '') {
    $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model['icon'], ['class' => 'media-object img-rounded pull-left', 'style' => 'width: 40px;']);
    //\appxq\sdii\utils\VarDumper::dump(Yii::getAlias('@storage')."/web/ezform/fileinput/" . $model['icon']);
    if(!\backend\modules\gantt\classes\GanttQuery::isUrlExist(Yii::getAlias('@storage')."/web/ezform/fileinput/" . $model['icon'])){
        $image_field = Html::img(Yii::getAlias('@storageUrl/images/pms_icon.png'), ['class' => 'media-object img-rounded pull-left', 'style' => 'width: 40px;']);
    }
}else{
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/pms_icon.png'), ['class' => 'media-object img-rounded pull-left', 'style' => 'width: 40px;']);
}

$userData = backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model['user_create']);
$image_user = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 40px;']);
if (\backend\modules\gantt\classes\GanttQuery::isUrlExist(Yii::getAlias('@storage/source/'.(isset($userData['avatar_path'])?$userData['avatar_path']:'')))) {
    $image_user = Html::img($userData['avatar_base_url'] .'/'. $userData['avatar_path'], ['class' => 'media-object img-rounded', 'style' => 'width: 40px;']);
}
$progress_style = "progress-bar-info";
if($model['progress']<25){
    $progress_style = "progress-bar-danger";
}else if($model['progress']>=25 && $model['progress']<50){
    $progress_style = "progress-bar-warning";
}else if($model['progress']==100){
    $progress_style = "progress-bar-success";
}

?>
<style>
    .classrow-listview:hover{
        background-color: #edf4fd;
        color: #3283e7;
        cursor: pointer;
    }
</style>

<div class="container-fluid classrow-listview" style="padding:10px;border-bottom:1px solid lightgray;" data-project_id="<?=$model['id']?>">
    <div class="col-md-4 classitem-view"><?= $image_field ?><span class="pull-left" style="font-size:16px;margin-left: 10px;"><?= $model['project_name'] ?></span></div>
    <div class="col-md-1 classitem-view"><?=$image_user?></div>
    <div class="col-md-1 classitem-view"><label style="font-size:16px;"><?= isset($task_amt[$model['id']])?$task_amt[$model['id']]:0;?></label></div>
    <div class="col-md-1 classitem-view"><label style="font-size:16px;color:skyblue"><?= (isset($shopping_amt[$model['id']])?$shopping_amt[$model['id']]:0)?></label>
        / <label style="font-size:16px;color:orange"><?= (isset($approve_amt[$model['id']])?$approve_amt[$model['id']]:0)?></label></div>
    <div class="col-md-1 classitem-view"><label style="font-size:16px;color:green;"><?= isset($complete_amt[$model['id']])?$complete_amt[$model['id']]:0;?></label></div>
    <div class="col-md-2 classitem-view">
        <div class="progress">
            <div class="progress-bar <?=$progress_style?>" role="progressbar" aria-valuenow="<?=$model['progress']?>"
                 aria-valuemin="0" aria-valuemax="100" style="width:<?=$model['progress']?>%">
                
            </div>
            
        </div>
        <label style="position: absolute;top:0px;margin-left: 40%;"><?= number_format($model['progress'],1)?> % Progress</label>
    </div>
    <div class="col-md-2">
        <?php if($ownProject || Yii::$app->user->can('administrator')):?>
        <?= Html::button("<i class='fa fa-clone'></i>  ".Yii::t('gantt', "Clone"),['class'=>'btn btn-info btn-xs btn_clone_maintask','data-dataid'=>$model['id']])?>
        <?= Html::button("<i class='fa fa-download'></i>  ".Yii::t('gantt', "Backup"),['class'=>'btn btn-default btn-xs btn_backup_maintask','data-dataid'=>$model['id']])?>
        <?= EzfHelper::btn($project_ezf_id)->label("<i class='fa fa-pencil'></i> ".Yii::t('gantt', "Update"))->reloadDiv($reloadDiv)->initdata(['pms_type'=>$tab,'flag_status'=>'2'])->options(['class'=>'btn btn-primary btn-xs'])->buildBtnEdit($model['id']) ?>
        <?= EzfHelper::btn($project_ezf_id)->label("<i class='fa fa-trash'></i> ".Yii::t('gantt', "Delete"))->reloadDiv($reloadDiv)->options(['class'=>'btn btn-danger btn-xs'])->buildBtnDelete($model['id']) ?>
        <?php endif;?>
    </div>
</div>
<div class="clearfix"></div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('.classitem-view').on('click',function(){
        var projectid= $(this).parent().attr('data-project_id');
        window.history.replaceState({}, $(this).html(), '<?= $href ?>&pms_tab=<?=$tab?>&pmsid='+projectid);
        var div = $('#gantt-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = $(this).parents('.pms-list-parent').attr('data-url');
        location.reload();
        //getUiAjax(url+'&project_id='+projectid, 'gantt-content');
    });
    
    
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>    
