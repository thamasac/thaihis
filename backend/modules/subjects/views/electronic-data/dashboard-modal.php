<?php

use \backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="modal-header">
    <h4 class="modal-title"><strong>CRFs for this visit</strong>
        <?php if($type == '2'): ?> 
            <label class="label label-success">Success</label>
        <?php elseif($type=='3'):?>
            <label class="label label-warning">Waiting</label>
        <?php elseif($type=='4'):?>
            <label class="label label-danger">Not Process</label>
        <?php elseif($type=='1'):?>
            <label class="label label-default">Form All</label>
        <?php endif;?>
    </h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div id="update_subject_link" data-url="<?= \yii\helpers\Url::to(['/subjects/subject-management/update-subjectlink','target'=>$target])?>"></div>
<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Form Name</th>
                    <th><?= Yii::t('app', 'Open Form') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $formList = \appxq\sdii\utils\SDUtility::string2Array($form_list);
                
                foreach ($formList as $key => $value) :
                    $ezform = EzfQuery::getEzformOne($value);
                    if ($ezform):
                        $dataForm = SubjectManagementQuery::GetTableData($ezform, " (`target`='{$target}' OR `subject_link`='{$target}') AND visit_link='{$visit_id}' ", 'one');
                        
                        ?>
                        <?php if ($type == '2' && $dataForm['rstat'] == '2'): ?>
                            <tr>
                                <td><?= $ezform['ezf_name'] ?></td>
                                <td>
                                    <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-primary btn-sx btn_open_form','data-ezf_id'=>$value])->dataid($dataForm['id'])->buildBtnEdit($dataForm['id']) ?>
                                </td>
                            </tr>
                        <?php elseif ($type == '3' && $dataForm['rstat'] == '1'): ?>
                            <tr>
                                <td><?= $ezform['ezf_name'] ?></td>
                                <td>
                                    <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-primary btn-sx btn_open_form','data-ezf_id'=>$value])->dataid($dataForm['id'])->buildBtnEdit($dataForm['id']) ?>
                                </td>
                            </tr>
                        <?php elseif ($type == '4' && !$dataForm): ?>
                            <tr>
                                <td><?= $ezform['ezf_name'] ?></td>
                                <td>
                                    <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-primary btn-sx btn_open_form','data-ezf_id'=>$value])->target($target)->initdata(['visit_name' => $visit_id])->buildBtnAdd() ?>
                                </td>
                            </tr>
                        <?php elseif ($type == '1'): ?>
                            <tr>
                                <td><?= $ezform['ezf_name'] ?></td>
                                <td>
                                    <?php if ($dataForm['rstat'] == '2'): ?>
                                        <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-success btn-sx btn_open_form','data-ezf_id'=>$value])->dataid($dataForm['id'])->buildBtnEdit($dataForm['id']) ?>
                                    <?php elseif ($dataForm['rstat'] == '1'): ?>
                                        <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-warning btn-sx btn_open_form','data-ezf_id'=>$value])->dataid($dataForm['id'])->buildBtnEdit($dataForm['id']) ?>
                                    <?php else: ?>
                                        <?= EzfHelper::btn($value)->reloadDiv('modal-content-formlist')->label("<i class='fa fa-pencil-square-o'></i>")->options(['class' => 'btn btn-danger btn-sx btn_open_form','data-ezf_id'=>$value])->target($target)->initdata(['visit_name' => $visit_id])->buildBtnAdd() ?>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endif;
                    endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

    $('.btn_open_form').click(function(){
        var ezf_id=$(this).attr('data-ezf_id');
        var url = $('#modal-content-formlist').attr('data-url-old');
        url = url+"&ezf_id="+ezf_id;
        $('#modal-content-formlist').attr('data-url',url);
        
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>