<?php
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" >SQL Viewer</h4>
</div>
<div class="modal-body">
  <?php if(isset($params_test) && !empty($params_test)):?>
  <div class="generate-sql-search">
      <?php $form = ActiveForm::begin([
          'id'  => 'generate-sql-form',
        'action' => ['/ezforms2/custom-config/sql-generate', 'ezf_id'=>$ezf_id, 'dataid'=>$dataid],
        'method' => 'GET',
	'layout' => 'horizontal',
	'fieldConfig' => [
	    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
	    'horizontalCssClasses' => [
		'label' => 'col-sm-2',
		'offset' => 'col-sm-offset-3',
		'wrapper' => 'col-sm-6',
		'error' => '',
		'hint' => '',
	    ],
	],
    ]); ?>
    
    <?php
foreach ($params_test as $key => $value) {
    $input_name = str_replace(':', '', $key);
  echo '<div class="form-group">'; 
  echo '<label class="col-sm-2 control-label">'.$input_name.'</label>';
  echo '<div class=" col-sm-6">';
   echo Html::textInput($input_name, $value, ['class'=>'form-control']);
   echo '</div>';
   echo '</div>';
}
    ?>
    
    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
  <?php endif;?>
    <?php
    echo \appxq\sdii\widgets\GridView::widget([
        'id' => 'generate-sql-grid',
        'dataProvider' => $provider,
    ]);
   ?>
</div>

<?php
$this->registerJs("
    $('#generate-sql-form').on('beforeSubmit', function(e) {
        var \$form = $(this);
        $.ajax({
            method: 'GET',
            url: \$form.attr('action'),
            data: \$form.serialize(),
            dataType: 'JSON',
            success: function(result, textStatus) {
                if(result.status == 'success') {
                    $('#modal-ezform-info .modal-content').html(result.html);
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }
        });
        return false;
    });

    $('#generate-sql-grid .pagination a').on('click', function() {
        getAjax($(this).attr('href'));
        return false;
    });
    
    function getAjax(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'JSON',
            success: function(result, textStatus) {
                if(result.status == 'success') {
                    $('#modal-ezform-info .modal-content').html(result.html);
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }
        });
    }
");
?>