<?php

use yii\helpers\Url;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$model_noauto = \backend\modules\core\classes\CoreQuery::getOptions('input_noauto');
if (!$model_noauto) {
    $model_noauto = new \backend\modules\core\models\CoreOptions();
    $model_noauto->option_name = 'input_noauto';
    $model_noauto->input_label = 'input_noauto';
    $model_noauto->option_value = \appxq\sdii\utils\SDUtility::array2String([79, 84, 80, 82, 83, 912, 904, 905, 87]);
    $model_noauto->input_field = 'DropDownList';
    $model_noauto->input_order = 0;
    $model_noauto->autoload = 'no';
    $model_noauto->save();
}
$items_noauto = $model_noauto->option_value;
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="itemModalLabel">Question Library Lists</h4>
</div>

<div class="modal-body">
  <div class="form-group row">
    <div class="col-md-12">
      <div class="list-group-item div-lib-search" style="background-color: #e5e5e5;">
        <?=
        $this->render('_lib_search_lists', [
            'model' => $searchModel,
            'reloadDiv' => 'lib-item-lists',
            'ezf_id' => $ezf_id,
            'v' => $v,
        ]);
        ?>
      </div>
      <?php
      $url = Url::to(['/ezforms2/ezform-fields-lib/lib-lists', 'ezf_id' => $ezf_id, 'v' => $v]);
      echo Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', ['id' => 'lib-item-lists', 'data-url' => $url]);
      ?>
    </div>
  </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#lib-item-lists').on('click', '.pagination li a', function () { //Next 
      var url = $(this).attr('href');
        $.post(url,$('.div-lib-search form').serialize()).done(function(result) {
            $('#lib-item-lists').html(result);
        }).fail(function(e) {
            <?=SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');            
        });
      return false;
    });
    
    // JS script btn-add-input-lib
    $('#lib-item-lists').on('click', '.btn-add-input-lib', function () {
      modalEzformAutoLib($(this).attr('data-id'), $(this).attr('data-url'));
    });

    function modalEzformAutoLib(id, url) {
      let items_noauto = <?= $items_noauto ?>;
      let auto = 1;
      $('#modal-ezform .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $('#modal-ezform-version').modal('toggle');
      $('#modal-ezform-version .modal-content').html('');

      if (items_noauto.indexOf(parseInt(id)) != -1) {
        $('#modal-ezform').modal('show');
        
        auto = 0;
        
        $('#modal-ezform').on('hidden.bs.modal', function (e) {
          if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
          }
        });
      } else {
        $('body').waitMe({
          effect: 'facebook',
          text: '<?= Yii::t('ezform', 'Please wait...') ?>',
          bg: 'rgba(0,0,0,0.7)',
          color: '#FFF',
          maxSize: '',
          waitTime: -1,
          textPos: 'vertical',
          fontSize: '20px',
          source: '',
          onClose: function () {
            //$('#btn-line').trigger('click');
          }
        });
      }

      $.ajax({
        method: 'GET',
        data: {auto: auto},
        url: url,
        dataType: 'HTML',
        success: function (result, textStatus) {
          $('#modal-ezform .modal-content').html(result);
        }
      });
    }

    $(function () {
      $.ajax({
        method: 'POST',
        url: '<?= $url ?>',
        dataType: 'HTML',
        success: function (result) {
          $('#lib-item-lists').html(result);
        }
      });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>