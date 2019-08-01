<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfStarterWidget;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
\backend\assets\AppAsset::register($this);
?>
<div class="app-ezform">
    <div class="container-fluid">
        <?php EzfStarterWidget::begin(); ?>
        <?php
        $formName = 'ezform-' . $ezf_id;
        $form = EzActiveForm::begin([
                    'id' => $formName,
                    'options' => [
                        'enctype' => 'multipart/form-data',
                    ]
        ]);
        ?>
        <?php foreach (Yii::$app->session->getAllFlashes() as $message): ?>
            <div class="container-fluid" style="padding-top: 15px;">
                <?=
                \yii\bootstrap\Alert::widget([
                    'body' => $message['body'],
                    'options' => $message['options'],
                ])
                ?>
            </div>
                <?php endforeach; ?>
        <div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff">
            <div class="modal-header" style="background-color: #fff">
                <?php echo \yii\bootstrap\Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'New'), Url::to(['index', 'ezf_id' => $ezf_id, 'token' => $token]), ['class' => 'btn btn-success pull-right']); ?>
                <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
            </div>
            <div class="modal-body">

                <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
                    <?php
                    $inputVisible = [79, 80, 81, 82, 83];
                    echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this, $disable, $inputVisible);
                    ?>
                </div>
            </div>

        </div> 
        <div class="modal-footer" >
            <?php
            if ($dataid != '') {
                ?>
                <?php
                appxq\sdii\assets\Html2CanvasAsset::register($this);
                echo Html::button('<i class="glyphicon glyphicon-print"></i>', [
                    'id' => 'h2c',
                    'class' => 'btn btn-default ',
                    'target' => '_blank',
                ]);
                ?>

                <?php
                \richardfan\widget\JSRegister::begin([
                    //'key' => 'bootstrap-modal',
                    'position' => \yii\web\View::POS_READY
                ]);
                ?>
                <script>

                    $('#h2c').click(function () {
                        html2canvas($('#print-<?= $modelEzf->ezf_id ?>'), {
                            onrendered: function (canvas) {
                                var img = canvas.toDataURL('image/png');
                                $.ajax({
                                    method: 'POST',
                                    url: '<?= Url::to(['/ezforms2/drawing/canvas-image']) ?>',
                                    data: {type: 'data', image: img, name: 'h2c', '_csrf': '<?= Yii::$app->request->getCsrfToken() ?>'},
                                    dataType: 'JSON',
                                    success: function (result, textStatus) {
                                        if (result.status == 'success') {
                                            window.location.href = result.path + result.data;

                                        } else {
                                            <?= SDNoty::show('result.message', 'result.status') ?>
                                        }
                                    }
                                });
                            }
                        });
                    });

                </script>
                <?php \richardfan\widget\JSRegister::end(); ?>
                <?php
            }
            ?>

        <?= EzfFunc::genBtnEzformPage($model, $modelEzf) ?>

        </div>

<?php EzActiveForm::end(); ?>
<?php EzfStarterWidget::end(); ?>
    </div>
</div>

<?php
$jsAddon = '';

$disabledInput = '';

if ($model->rstat == 2) {
    $disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";
}

$this->registerJs("

{$modelEzf->ezf_js}
   
$disabledInput

");
?>