<div class="row">        
  <div class="col-sm-4"> 
    <label class="control-label">เล่มที่</label>
    <input readonly="" type="text" value="<?= isset($data['receipt_book_no']) ? $data['receipt_book_no'] : $data['receipt_book_no'] ?>" name="receipt_book_no" class="form-control"/>
  </div>
  <div class="col-sm-4 sdbox-col">
    <label class="control-label">เลขที่</label>
    <input readonly="" type="text" value="<?= isset($data['receipt_tr_no']) ? $data['receipt_tr_no'] : '' ?>" name="receipt_tr_no" class="form-control"/>
    <input type="hidden" value="<?= isset($data['receipt_no_id']) ? $data['receipt_no_id'] : ''; ?>" name="receipt_no_id" class="form-control"/>
  </div>
  <div class="col-sm-4 sdbox-col">
    <label class="control-label">แก้ไข</label>
    <?php
    $initdata = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['receipt_user_id' => $user_id]);
    $url = \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform',
                'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv
                , 'dataid' => isset($data['receipt_no_id']) ? $data['receipt_no_id'] : '', 'initdata' => $initdata
    ]);

    echo yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span> แก้ไข', 'javascript:voie(0)', [
        'data-url' => $url,
        'data-modal' => 'modal-ezform-main',
        'title' => Yii::t('yii', 'Edit'),
        'class' => 'btn btn-primary btn-block ezform-main-open',
    ]);

//    echo backend\modules\ezforms2\classes\BtnBuilder::btn()
//            ->ezf_id('1527664185052797300')
//            ->initdata(['receipt_user_id' => $user_id,])
//            ->reloadDiv($reloadDiv)
//            ->label('<i class="glyphicon glyphicon-pencil"></i> แก้ไข')->options(['class' => 'btn btn-primary btn-block'])
//            ->buildBtnEdit($data['receipt_no_id']);
    ?>
  </div>
</div>