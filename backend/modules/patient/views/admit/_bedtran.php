<?php

use backend\modules\patient\classes\PatientHelper;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
?>
<div id="show-bed-tran">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-md-12">
            <?php
            $admit_id = $target;
            
            echo PatientHelper::uiBtnADT($admit_id, $visit_id, $reloadDiv, 'I', (isset($modelBedtran[0]['id']) ? $modelBedtran[0]['id'] : ''));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center;">#</th>
                        <th><?= Yii::t('patient', 'Room') ?></th>
                        <th><?= Yii::t('patient', 'Ward') ?></th>
                        <th><?= Yii::t('patient', 'Status') ?></th>
                        <th><?= Yii::t('patient', 'Date') ?></th>
                        <th></th>
                </thead>
                <tbody>
                    <?php
                    if ($modelBedtran) {
                        $modelField = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name' => 'bed_tran_status'])->one();
                        if (isset(Yii::$app->session['ezf_input'])) {
                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelField['ezf_field_type'], Yii::$app->session['ezf_input']);
                        }

                        foreach ($modelBedtran as $index => $value) {
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $value['bed_name'] ?></td>
                                <td><?= $value['sect_name'] ?></td>
                                <td><?= backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField, $value) ?></td>
                                <td><?= $value['create_date'] ?></td>                                
                                <td style="width:50px;text-align: center;">                                    
                                    <?php
                                    if ($value['bed_tran_status'] == '1') {
                                        $url = \yii\helpers\Url::to(['/ezforms2/ezform-data/delete', 'ezf_id' => $ezf_id, 'dataid' => $value['id'], 'reloadDiv' => $reloadDiv]);
                                        echo \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii', 'Delete'), $url, [
                                            'class' => 'btn btn-danger btn-sm btn-del-bed',
                                        ]);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6"><div class="empty">ยังไม่มีรายการห้องพัก</div></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$this->registerJS("
        
//        function modalEzformMain(url, modal) {
//            $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//            $('#' + modal).modal('show')
//                    .find('.modal-content')
//                    .load(url);
//        }
        
        $('#$reloadDiv table tbody tr td .btn-del-bed').on('click', function(){
            var url = $(this).attr('href');
            var urlreload = $('#$reloadDiv').attr('data-url');

            yii.confirm('" . Yii::t('yii','Are you sure you want to delete this item?') . "', function(){
                    $.post(url).done(function(result) {
                        if(result.status == 'success') {
                            " . SDNoty::show('result.message', 'result.status') . "
                             getUiAjax(urlreload, '$reloadDiv');
                        } else {
                            " . SDNoty::show('result.message', 'result.status') . "
                        }
                    }).fail(function() {
                        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                        console.log('server error');
                    });
            });
            return false;
        });

    ");
?>