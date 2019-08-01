<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$tablist = [
    1 => Yii::t('ezform', 'My Forms'),
    2 => Yii::t('ezform', 'Co-Creator Forms'),
    3 => Yii::t('ezform', 'Public Forms'),
    4 => Yii::t('ezform', 'Assign Forms'),
    7 => Yii::t('ezform', 'All My EzForms'),
    5 => Yii::t('ezform', 'Favorite Forms'),
    6 => Yii::t('ezform', 'Trash Forms'),
];
?>
<div id="box-ezform-crf" class="ezform-crf" data-url="<?= Url::current()?>">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">EzForm CRF</h4>
  </div>
  <div class="modal-body">
      <?php
      
      echo GridView::widget([
          'id' => 'ezform-crf-grid',
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'columns' => [
              [
                  'class' => 'yii\grid\SerialColumn',
                  'headerOptions' => ['style' => 'text-align: center;'],
                  'contentOptions' => ['style' => 'width:60px;text-align: center;'],
              ],
              [
                  'attribute' => 'ezf_icon',
                  'value' => function ($data) {
                      return backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($data, 25);
                  },
                  'format' => 'raw',
                  'headerOptions' => ['style' => 'text-align: center;'],
                  'contentOptions' => ['style' => 'width:40px; text-align: center;'],
                  'filter' => '',
              ],
              'ezf_name',
              //'ezf_detail:ntext',
              
              [
                  'attribute' => 'created_at',
                  'format' => ['date', 'php:d/m/Y'],
                  'contentOptions' => ['style' => 'width:100px;text-align: center;'],
                  'filter' => '',
              ],
              [
		'attribute'=>'ezf_options',
		'label'=>'Lock Date',
                'format' => ['date', 'php:d/m/Y'],
		'value'=>function ($data){ 
                      $ezf_options = appxq\sdii\utils\SDUtility::string2Array($data['ezf_options']);
                      $lock_date = isset($ezf_options['lock_date'])?$ezf_options['lock_date']:NULL;
                      return $lock_date; 
                },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                 'filter' => '',
                ],
              [
                'class' => 'backend\modules\ezforms2\classes\ActionColumn',
                'template' => '{lock} {export} {dictionary} {annotated}',
                'buttons' => [
                    'annotated' => function ($url, $data, $key) {
                        
                        return EzfHelper::btn($data->ezf_id)->options([
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'annotated',
                                'title' => Yii::t('ezform', 'Annotated CRF'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnAnnotated();
                    },
                            
                    'dictionary' => function ($url, $data, $key) {
                        return EzfHelper::btn($data->ezf_id)->options([
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'dictionary',
                                'title' => Yii::t('ezform', 'Dictionary'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnDictionary();
                    },
                           
                    'export' => function ($url, $data, $key) {
                        $options = appxq\sdii\utils\SDUtility::string2Array($data->ezf_options);
                        if(isset($options['lock_data']) && $options['lock_data']==1){
                            return Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('ezform', 'Data'), Url::to(['/ezforms2/data-lists/export',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'export',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-warning btn-xs'        
                                    ]);
                        } else {
                            return '';
                        }
                        
                    },
                    'lock' => function ($url, $data, $key) {
                        $options = appxq\sdii\utils\SDUtility::string2Array($data->ezf_options);
                        if(isset($options['lock_data']) && $options['lock_data']==1){
                            return Html::button('<span class="glyphicon glyphicon-off"></span> '.Yii::t('ezform', 'Unlock Data'),  [
                                    'data-action' => 'export',
                                    'data-url' => Url::to(['/ezforms2/ezform/lock', 'id'=>$data->ezf_id, 'status'=>0]),   
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-danger btn-xs btn-lock'        
                                    ]);
                        } else {
                            return Html::button('<span class="glyphicon glyphicon-off"></span> '.Yii::t('ezform', 'Lock Data'), [
                            'data-action' => 'export',
                            'data-url' => Url::to(['/ezforms2/ezform/lock', 'id'=>$data->ezf_id, 'status'=>1]),    
                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            'class' => 'btn btn-success btn-xs btn-lock'
                            ]);
                        }
                        
                    },
                ],
                'contentOptions' => ['style' => 'width:350px;'],
            ]
          ],
      ]);
      ?>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
$('.btn-lock').on('click', function() {
    let url = $(this).attr('data-url');
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'JSON',
        success: function(result, textStatus) {
            getUiAjax($('#box-ezform-crf').attr('data-url'), '#modal-ezform-crf .modal-content');
        }
    });
    return false;
});

$('#ezform-crf-grid').on('beforeFilter', function(e) {
    var $form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: $form.attr('action'),
        data: $form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#modal-ezform-crf .modal-content').html(result);
	}
    });
    return false;
});

$('#ezform-crf-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '#modal-ezform-crf .modal-content');
    return false;
});

$('#ezform-crf-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '#modal-ezform-crf .modal-content');
    return false;
});

function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $(divid).html(result);
        }
    });
}
</script>
<?php \richardfan\widget\JSRegister::end(); ?>