<div>
    <?php if ($data) { ?>    
      <strong class="text-danger">ข้อมูลแพ้ยา </strong>
      <?php foreach ($data as $value) { ?>
          <strong><?= $value['ezf_choicelabel']; ?> </strong>
          <span class="text-danger"><?= $value['drug_allergy']; ?> </span>        
          <?php
      }
  }
  ?>
  <?=
          \backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezf_id)
          ->reloadDiv($reloadDiv)
          ->label('<i class="glyphicon glyphicon-th-list"></i>')
          ->options(['class' => 'btn btn-default btn-sm'])
          ->target($pt_id)
          ->modal('modal-grid-drugallergy')
          ->buildBtnGrid();
  ?>
</div>

<div style="margin-top:5px;">
  <strong>เพิ่มข้อมูล แพ้ยา </strong> 
  <?php
  echo backend\modules\ezforms2\classes\BtnBuilder::btn()
          ->ezf_id($ezf_id)
          ->reloadDiv($reloadDiv)->label('<i class="glyphicon glyphicon-plus"></i>')
          ->options(['class' => 'btn btn-success btn-sm'])->target($pt_id)
          ->buildBtnAdd();
  ?>
</div>  
<?php
\appxq\sdii\widgets\ModalForm::begin([
    'id' => 'modal-grid-drugallergy',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);

$this->registerJS("
    $(document).on('hide.bs.modal','#modal-grid-drugallergy', function () {
        location.reload();
   });
");
?>
