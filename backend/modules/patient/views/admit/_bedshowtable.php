<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

//\backend\modules\patient\assets\PatientAsset::register($this);
?>
<style>
    .alert-patient:hover{
        border-color: #9ea0a1;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">Ward room</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">            
            <div id="listview-beds">
                <?= $this->render('_searchbed', ['model' => $searchModel, 'deptIPD' => $deptIPD, 'ezf_id' => $ezf_id, 'target' => $target, 'dataid' => $dataid, 'reloadDiv' => $reloadDiv, 'modal' => $modal]) ?>
                <?=
                \yii\widgets\ListView::widget([
                    'id' => 'listview-bed',
                    'dataProvider' => $dataProvider,
                    //itemOptions' => ['style' => 'float: left;margin-left:0px;margin-right:15px;'],
                    'itemOptions' => ['class' => 'col-xs-2'],
                    'layout' => '<hr>{items}<div class="list-pager">{pager}</div>',
                    'itemView' => function ($model) {
                        return $this->render('_itembed', [
                                    'model' => $model,
                        ]);
                    },
                ])
                ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php
$urlSelect = yii\helpers\Url::to(['/patient/emr/bed-select', 'ezf_id' => $ezf_id, 'dataid' => $dataid, 'target' => $target]);
$this->registerJS("
    $('#listview-bed').on('click','.btn',function(){
    $('#listview-bed .btn').removeClass('btn');
         var code = $(this).attr('data-code');
         $.get('$urlSelect', {code: code}).done(function (result) {
            " . SDNoty::show('result.message', 'result.status') . "
            var url = $('#$reloadDiv').attr('data-url');
            $(document).find('#$modal').modal('hide');
            getUiAjax(url, '$reloadDiv');
        }).fail(function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                    console.log('server error');
        });
        return false;
    });
    ");
?>