<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$this->title = Yii::t('app', 'Report Admin');
?>
<div class="report-body"> 
    <div class="sdbox-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
<?= $this->render('_search', ['model' => $searchModel]) ?>
    <div class="report-content">

    </div>
    <div class="report-export">

    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>

    function actionGet(url, action, data) {
        $('.report-content').empty();
        $.post(url, data).done(function (result) {
            if (action === 'search') {
                $('.report-content').html(result);
            } else {
                $('.report-export').html(result);
            }
        }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>;
            console.log('server error');
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>