<?php
use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\TbdataAll;
$ezf_id = $options['ezf_id'];
$field_value = $options['field_value'];
$field_label = $options['field_label'];

echo \backend\modules\ezforms2\classes\TmfWidget_backup::ui()
        ->ezf_id($ezf_id)
        ->pageSize($options['page_size'])
        ->dataOptions($options)
        ->reloadDiv('tmf')
        ->buildUi();
?>

<?php 


?>
