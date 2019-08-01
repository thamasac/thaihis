<?php

//    public static function loadTbData($ezf_table, $dataid) {
use backend\modules\ezforms2\models\TbdataAll;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

try {
    $ezform = \backend\modules\ezforms2\models\Ezform::find()->where(["ezf_id" => $options["ezf_id"]])->one();
    $model = new TbdataAll();
    $model->setTableName($ezform["ezf_table"]);
    $model = $model->find()->where('rstat <> 3')->all();
    if (!$model) {
        return FALSE;
    }
//            return $model;
} catch (\yii\db\Exception $e) {
    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
    return FALSE;
}

?>

<div>
    <div class="panel panel-primary">
        <div class="panel-heading"><?= $options['title'] ?></div>
        <div class="panel-body">
            <div class="form-group row">
                <div class="col-md-6 ">
                    <?php
                    $attrname_data_id = 'options[data_id]';
                    $value_data_id = isset($options["data_id"]) ? $options["data_id"] : '';
                    Html::label(Yii::t('ezform', 'Form'), $attrname_data_id, ['class' => 'control-label']);
                    try {
                        echo kartik\select2\Select2::widget([
                            'name' => $attrname_data_id,
                            'value' => "",
                            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => "config_data_id"],
                            'data' => ArrayHelper::map($model,'id',  $options["search_field"]),
                            'pluginOptions' => [

                            ],
                        ]);
                    } catch (Exception $e) {
                        echo "Widget not work property.";
                    }
                    ?>
                </div>

            </div>

            <div id="ref_field_box">

            </div>
        </div>
    </div>
</div>

<?php
$ezf_id = $options["ezf_id"];
$ezf_table = $ezform["ezf_table"];
$fields = json_encode($options['fields']);
$labels = json_encode($options['labels']);
$this->registerJS("
    fields($('#config_ezf_id').val());
    
    $('#config_data_id').on('change',function(){
      var ref_id = $(this).val();
      var ezf_table = '$ezf_table';
      var ezf_id = '$ezf_id';
      fields(ezf_id,ref_id ,ezf_table,);
    });
    
    function fields(ezf_id,ref_id,ezf_table){
        $.post('".Url::to(['/ezwidget/ez-widget/get-fieldlist'])."',{ ezf_id: ezf_id, ezf_table: ezf_table, ref_id: ref_id, fields:$fields, labels:$labels}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>

