<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;
use backend\modules\ezmodules\classes\ModuleFunc;
use appxq\sdii\helpers\SDHtml;

backend\modules\ezmodules\assets\StarRatingsAsset::register($this);
?>

<div class="inv-form">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Module Information') ?></h4>
    </div>

    <div class="modal-body">
        <?php
        $linkModule = '';
        if ($model['ezm_type'] == 1) {
            $linkModule = \yii\helpers\Url::to($model['ezm_link']);
        } else {
            $linkModule = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id' => $model['ezm_id']]);
        }

        $linkFav = Html::button('<i class="glyphicon glyphicon-cloud-download"></i> ' . Yii::t('app', 'Discard'), [
                    'class' => 'btn btn-success btn-sm  fav-btn',
                    'data-id' => $model['ezm_id'],
        ]);

        $linkNotFav = Html::button('<i class="glyphicon glyphicon-cloud-download"></i> ' . Yii::t('ezmodule', 'GET'), [
                    'class' => 'btn btn-warning btn-sm  fav-btn',
                    'data-id' => $model['ezm_id'],
        ]);

        $gname = Html::encode($model['ezm_name']);

        $userId = Yii::$app->user->id;
        $dataFav = \backend\modules\ezmodules\classes\ModuleQuery::getFavModule($userId);
        $arrayMap = \yii\helpers\ArrayHelper::map($dataFav, 'ezm_id', 'ezm_name');
        $arrayFav = array_keys($arrayMap);
        ?>
        <div class="media">
            <div class="media-left">
                <a href="<?= $private == 1 ? '#' : $linkModule ?>">
                    <img width="72" height="72" class="media-object img-rounded" src="<?= (isset($model['ezm_icon']) && $model['ezm_icon'] != '') ? $model['icon_base_url'] . '/' . $model['ezm_icon'] : ModuleFunc::getNoIconModule() ?>" >
                </a>
            </div>
            <div class="media-body">

                <?php //if ($private != 1): ?>
                    <div id="btn-box" class="pull-right">

                        <?php if ($model['ezm_system'] != 1): ?>
                            <?php if (in_array($model['ezm_id'], $arrayFav)): ?>
                                <?= $linkFav ?>
                            <?php else: ?>
                                <?= $linkNotFav ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php //endif; ?>

                <h4 class="media-heading"><?= $gname ?></h4>
                <span style="color: #666">
                    <?= Yii::t('ezmodule', 'Updated app') ?>, <?= isset($model['updated_at']) ? \appxq\sdii\utils\SDdate::mysql2phpDate($model['updated_at']) : '?' ?><br>
                    <?= Yii::t('ezmodule', 'Number of users') ?> <code><?= number_format($ncount['user']); ?></code> <?= Yii::t('ezmodule', 'people') ?>, <?= Yii::t('ezmodule', 'From') ?> <code><?= number_format($ncount['org']); ?></code> <?= Yii::t('ezmodule', 'department') ?> <br>
                    <?php if (isset($pcoc)) { ?> <?= Yii::t('ezmodule', 'Patient care') ?> <code><?= number_format($pcoc['npatient']); ?></code> <?= Yii::t('ezmodule', 'people') ?>, <?= Yii::t('ezmodule', 'Total') ?> <code><?= number_format($pcoc['ntime']); ?></code> <?= Yii::t('ezmodule', 'time') ?> <?php } ?>
                </span>

                <div class="form-inline">
                    <div style="display: inline-block; position: relative;">
                    <?= Html::dropDownList('rating_star_view', $star, [0, 1, 2, 3, 4, 5], ['id' => 'rating_star_view']) ?>
                    <?php if($total>0){?><span style="color: #666; position: absolute; top: 0px; left: 105px;">(<?=$total?>)</span><?php } ?>
                    </div>
                </div>
                
                
            </div>
        </div>
        <br>
        <ul id="myTabs" class="nav nav-tabs nav-justified">
            <li role="presentation" class="active"><a href="#page1" id="page1-tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Details') ?></a></li>
            <li role="presentation"><a href="#page2" id="page2-tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Reviews') ?></a></li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade in active" role="tabpanel" id="page1" aria-labelledby="page1-tab">
                <div class="modal-header">
                    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Details') ?></h4>
                </div>
                <div class="modal-body">
                    <?= $model['ezm_detail'] ?>
                </div>

                <div class="modal-header">
                    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Development Team') ?></h4>
                </div>
                <div class="modal-body">
                    <?= $model['ezm_devby'] ?>
                </div>

                <div class="modal-header">
                    <?php
                    $userProfile = Yii::$app->user->identity->profile;
                    $fullname = isset($userProfile['firstname'])?$userProfile['firstname']:'';
                    $fullname .= isset($userProfile['lastname'])?' '.$userProfile['lastname']:'';
                    ?>
                    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'More by') .' '. $fullname ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <?php
                        $dataModules = \backend\modules\ezmodules\classes\ModuleQuery::getMoreModule($model['created_by'], $model['ezm_id']);
                        echo $this->render('_item', [
                            'model' => $dataModules,
                            'mode' => 1,
                        ]);
                        ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" role="tabpanel" id="page2" aria-labelledby="page2-tab">
                <div style="margin-top: 15px;"></div>
                <div id="comment-box"></div>
                <hr>
                <div id="comment-list-box">
                    <ul id="comment-list" class="media-list"> 

                    </ul>
                </div>
            </div> <!-- end -->
        </div>
    </div>

</div>

<?php $this->registerJs("
$('#rating_star_view').barrating({
    theme: 'bootstrap-stars',
    readonly:true,
    allowEmpty:true, 
     emptyValue:0,
});

$('.inv-form').on('click', '.fav-btn', function() {
    var id = $(this).attr('data-id');
    var icon = $('#btn-box');
    $.ajax({
	method: 'POST',
	url: '" . Url::to(['/ezmodules/default/favorite', 'id' => $model['ezm_id']]) . "',
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
                reloadNow = 1;
		" . SDNoty::show('result.message', 'result.status') . "
		if(result.active==1){
		    icon.html('$linkFav');
		} else {
		    icon.html('$linkNotFav');
		}
	    } else {
		" . SDNoty::show('result.message', 'result.status') . "
	    }
	}
    });
});

getComment();
getCommentList();

function getComment() {
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezmodules/default/comment', 'ezm_id' => $model['ezm_id']]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#comment-box').html(result);
                }
            });
        }
        
function getCommentList() {
    $('#more_comment').attr('data-start', 20);
    
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezmodules/default/comment-list', 'ezm_id' => $model['ezm_id']]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#comment-list').html(result);
                }
            });
        }
        
$('.inv-form').on('click', '#more_comment', function(){
    var start = parseInt($(this).attr('data-start'));
    $('.more-item').remove();
    getMoreCommentList(start);
});

function getMoreCommentList(start) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/default/comment-list', 'ezm_id' => $model['ezm_id'], 'start' => '']) . "'+start,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#comment-list').append(result);
        }
    });
}
        
$('.inv-form').on('click', '.commt-del-btn', function(){
    var id = $(this).attr('data-id');
    var btn = $(this);
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        delcommt(id)
        btn.parent().remove();
    });
    
});

function delcommt(id) {
    $.post(
       '" . yii\helpers\Url::to(['/ezmodules/default/delete-commt']) . "',
       {id:id}
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
    } else {
        " . SDNoty::show('result.message', 'result.status') . "
    } 
    }).fail(function() {
        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
        console.log('server error');
    });
}

    ");
?>