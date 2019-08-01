<div class="panel panel-default">
  <div class="panel-body">              
    <?php
    $ezfPackage_table = \backend\modules\patient\Module::$formTableName['pis_package'];
    $ezfPackage_id = \backend\modules\patient\Module::$formID['pis_package'];
    $dataPackage = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezfPackage_table, $item_dataid);
    ?>
    Package Name : <strong><?= $dataPackage['package_name'] ?></strong> 
    <?=
            backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezfPackage_id)
            ->reloadDiv($reloadDiv)
            ->label('<span class="glyphicon glyphicon-trash"></span>')
            ->modal($modal)
            ->options([
                'title' => Yii::t('yii', 'Delete'),
                'class' => 'btn btn-danger btn-xs',
            ])
            ->buildBtnDelete($item_dataid) . ' ' .
            backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezfPackage_id)
            ->reloadDiv($reloadDiv)
            ->label('<span class="glyphicon glyphicon-pencil"></span>')
            ->modal($modal)
            ->options([
                'title' => Yii::t('yii', 'Edit'),
                'class' => 'btn btn-primary btn-xs',
            ])
            ->buildBtnEdit($item_dataid)
    ?>
  </div>
</div>