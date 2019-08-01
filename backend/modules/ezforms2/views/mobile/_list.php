<?php

use appxq\sdii\widgets\GridView;
use appxq\sdii\helpers\SDNoty;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="selete-ezform-fav">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezform', 'EzMobile Forms')?></h4>
    </div>
    <div class="modal-body">
        <?php
    $items = [
        [ 
            'label' => '<i class="glyphicon glyphicon-home"></i> '.Yii::t('ezform', 'My Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/mobile/list', 'tab' => '1']),
            'encode'=>false,
            'active' => $tab == '1',
            'template' => '{update} {trash}'
        ],
        [
            'label' => '<i class="fa fa-users"></i> '.Yii::t('ezform', 'Co-Creator Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/mobile/list', 'tab' => '2']),
            'encode'=>false,
            'active' => $tab == '2',
            'template' => '{update}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-globe"></i> '.Yii::t('ezform', 'Public Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/mobile/list', 'tab' => '3']),
            'active' => $tab == '3',
            'encode'=>false,
            'template' => '{view}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-send"></i> '.Yii::t('ezform', 'Assign Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/mobile/list', 'tab' => '4']),
            'active' => $tab == '4',
            'encode'=>false,
            'template' => '{insert} {view}'
        ],
    ];
    ?>
        <?=
    \yii\bootstrap\Nav::widget([
        'id'=>'tap-ezform-fav',
        'items' => $items,
        'options' => ['class' => 'nav nav-tabs', 'style' => 'margin: 10px 0px;'],
    ])
    ?>
        <?php
    echo GridView::widget([
        'id' => 'ezform-fav-grid',
        'panelBtn' => '',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ezf_id',
                'label'=> Yii::t('ezform', 'EzMobile'),
                'value'=> function ($data) use ($modelFav){
                    if(isset($modelFav) && !empty($modelFav)){
                        foreach ($modelFav as $value) {
                            if($value['ezf_id'] == $data['ezf_id']){
                                return yii\helpers\Html::a('<i class="glyphicon glyphicon-phone"></i>', \yii\helpers\Url::to(['/ezforms2/mobile/add', 'ezf_id'=>$data['ezf_id']]),['style'=>'font-size: 30px;color:#17a2b8;']);
                            }
                        }
                    }
                    
                    return yii\helpers\Html::a('<i class="glyphicon glyphicon-unchecked"></i>', \yii\helpers\Url::to(['/ezforms2/mobile/add', 'ezf_id'=>$data['ezf_id']]),['style'=>'font-size: 30px;color:grey;']);
                },
                'format'=>'raw',        
                'filter' => '',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:80px;text-align: center;'],        
            ],
            'ezf_name',
            [
                'attribute' => 'ezf_detail',
                'format' => 'ntext',
                'filter' => '',
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y'],
                'filter' => '',
            ],
        ],
    ]);
    ?>
    </div>
</div>

<?php

//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
$reloadDiv = 'modal-ezform-favorite .modal-content';
$this->registerJs("

$('#ezform-fav-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var star = $(this).find('i').hasClass('glyphicon-phone');
    
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'JSON',
        success: function(result, textStatus) {
            if(result.status != 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
            } 
        }
    });
    

    if(star){
        $(this).find('i').removeClass('glyphicon-phone');
        $(this).find('i').addClass('glyphicon-unchecked');
        $(this).find('i').css('color', 'grey');
    } else {
        $(this).find('i').addClass('glyphicon-phone');
        $(this).find('i').removeClass('glyphicon-unchecked');
        $(this).find('i').css('color', '#17a2b8');
    }
    
    return false;
});

$('#ezform-fav-grid').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#tap-ezform-fav li a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#ezform-fav-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#ezform-fav-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

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

");
?>