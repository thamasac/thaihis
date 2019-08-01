<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

//appxq\sdii\utils\VarDumper::dump($reloadDiv);
?>
<div id="view-use-set">
  <div class="row" style="margin-bottom: 15px">
    <div class="col-md-12">
        <?php
        echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezf_id)->target($generic_id)
                ->label('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'New'))
                ->options(['class' => 'btn btn-sm btn-info'])->modal('modal-use-set')
                ->initdata(['use_item_id' => $item_id, 'use_addedit_status' => '1'])->reloadDiv($reloadDiv)
                ->buildBtnAdd();
        ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th style="text-align: center;">#</th>
            <th>วิธีใช้</th>
            <th>Active</th>
            <th>Edit</th>
            <th>Delete</th>
        </thead>
        <tbody>
            <?php
            if ($model) {
                foreach ($model as $index => $value) {
                    ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $value['drug_use'] ?></td>
                    <td>
                      <label>
                          <?=
                          yii\bootstrap\Html::checkbox('drug_use_active', ($value['use_active'] == '1') ? TRUE : FALSE, ['dataid' => $value['useset_id']])
                          . ' ' . yii\bootstrap\Html::tag('span', 'Active')
                          ?>
                      </label>
                    </td>
                    <td style="width:50px;text-align: center;">                                    
                        <?php
                        $initdata = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['use_addedit_status' => '2']); //1 = add , 2 = edit
                        $url = \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'dataid' => $value['useset_id'], 'reloadDiv' => $reloadDiv, 'modal' => 'modal-use-set', 'initdata' => $initdata]);
                        echo \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Edit'), '#', [
                            'class' => 'btn btn-primary btn-sm ezform-main-open',
                            'data-modal' => 'modal-use-set', 'data-url' => $url
                        ]);
                        ?>
                    </td>                       
                    <td style="width:50px;text-align: center;">                                    
                        <?php
                        $url = \yii\helpers\Url::to(['/ezforms2/ezform-data/delete', 'ezf_id' => $ezf_id, 'dataid' => $value['useset_id'], 'reloadDiv' => $reloadDiv]);
                        echo \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii', 'Delete'), $url, [
                            'class' => 'btn btn-danger btn-sm btn-del-use',
                        ]);
                        ?>
                    </td>
                  </tr>
                  <?php
              }
          } else {
              ?>
              <tr>
                <td colspan="6"><div class="empty">ยังไม่มีรายการวิธีใช้</div></td>
              </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$urlActive = yii\helpers\Url::to(['/pis/pis-item/use-item-active', 'generic_id' => $generic_id, 'item_id' => $item_id]);
$this->registerJS("
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
        
        $('#$reloadDiv').on('click','.ezform-main-open', function () {
            var url = $(this).attr('data-url');
            var modal = $(this).attr('data-modal');
            modalEzformMain(url, modal);
            return false;
        });

        function modalEzformMain(url, modal) {
            $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#' + modal).modal('show')
                    .find('.modal-content')
                    .load(url);
        }
        
        $('#$reloadDiv table tbody tr td .btn-del-use').on('click', function(){
            var url = $(this).attr('href');
            var urlreload = $('#$reloadDiv').attr('data-url');

            yii.confirm('" . Yii::t('yii', 'Are you sure you want to delete this item?') . "', function(){
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

        $('#view-use-set input[type=\"checkbox\"]').on('change', function(){
        if($(this).is(':checked')){
            var dataid = $(this).attr('dataid');
            $.get('$urlActive',{dataid:dataid}).done(function (result) {
                if(result.status == 'success') {
                    " . SDNoty::show('result.message', 'result.status') . "
                    var urlreload = $('#$reloadDiv').attr('data-url');
                    getUiAjax(urlreload, '$reloadDiv');
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }).fail(function() {
                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                console.log('server error');
            });
        }
        })
    ");
?>