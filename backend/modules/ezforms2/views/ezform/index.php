<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\appxq\sdii\widgets\FileInput::widget(['name'=>'register_file_input']);

$this->title = Yii::t('app', 'EzForm');

$tablist = [
    1 => Yii::t('ezform', 'My Forms'),
    2 => Yii::t('ezform', 'Co-Creator Forms'),
    3 => Yii::t('ezform', 'Public Forms'),
    4 => Yii::t('ezform', 'Assign Forms'),
    7 => Yii::t('ezform', 'All My EzForms'),
    5 => Yii::t('ezform', 'Favorite Forms'),
    6 => Yii::t('ezform', 'Trash Forms'),
];

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = isset($tablist[$tab])?$tablist[$tab]:'';

?>
<div class="ezform-index">
    <div class="sdbox-header">
      
      <h3><?= Html::encode($this->title) ?> <a href="<?= Url::to(['/ezforms2/ezform/input-help'])?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('ezform', 'EzForm Question Types')?></a></h3>
    </div>
    <?php
    $items = [
        0 => [
            'label' => '<i class="glyphicon glyphicon-home"></i> ' . Yii::t('ezform', 'My Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '1']),
            'encode' => false,
            'active' => $tab == '1',
            'template' => '{er} {datalist} {meta} {export}  {token} {update} {trash}'
        ],
        1 => [
            'label' => '<i class="fa fa-users"></i> ' . Yii::t('ezform', 'Co-Creator Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '2']),
            'encode' => false,
            'active' => $tab == '2',
            'template' => '{datalist} {meta} {export}  {token} {update}'
        ],
        2 => [
            'label' => '<i class="glyphicon glyphicon-globe"></i> ' . Yii::t('ezform', 'Public Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '3']),
            'active' => $tab == '3',
            'encode' => false,
            'template' => '{datalist} {annotated} {dictionary} {clone}'
        ],
        3 => [
            'label' => '<i class="glyphicon glyphicon-send"></i> ' . Yii::t('ezform', 'Assign Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '4']),
            'active' => $tab == '4',
            'encode' => false,
            'template' => '{datalist} {annotated} {dictionary} {clone}'
        ],
        6 => [
            'label' => '<i class="glyphicon glyphicon-list"></i> ' . Yii::t('ezform', 'All My EzForms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '7']),
            'active' => $tab == '7',
            'encode' => false,
            'template' => '{datalist} {annotated} {dictionary} {clone}'
        ],
        4 => [
            'label' => '<i class="glyphicon glyphicon-star"></i> ' . Yii::t('ezform', 'Favorite Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '5']),
            'active' => $tab == '5',
            'encode' => false,
            'template' => '{datalist} {annotated} {dictionary} {clone}'
        ],
        5 => [
            'label' => '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('ezform', 'Trash Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '6']),
            'active' => $tab == '6',
            'encode' => false,
            'template' => '{undo} {delete}'
        ],
    ];
    ?>
    <div class="pull-right" style="margin-top: 10px;">
        <?= Html::a('<i class="fa fa-check-circle"></i> ' . Yii::t('ezform', 'EzForm Version Management'), ['/ezforms2/ezform-version/index'], ['class' => 'btn btn-warning btn-flat']) ?> 
        <?= Html::a('<i class="glyphicon glyphicon-import"></i> ' . Yii::t('ezform', 'Restore EzForm'), ['/ezforms2/ezform/import'], ['class' => 'btn btn-info btn-flat']) ?>
    </div>

    <?=
    \yii\bootstrap\Nav::widget([
        'items' => $items,
        'options' => ['class' => 'nav nav-tabs', 'style' => 'margin: 10px 0px;'],
    ]);
    ?>
  <div class="row" style="margin-bottom: 15px; ">
    <div class="col-md-12">
      <?php
            echo Html::button(SDHtml::getBtnAdd() . ' '. Yii::t('ezform', 'Create EzForm'), ['data-url' => Url::to(['ezform/add']), 'class' => 'btn btn-success', 'id' => 'modal-addbtn-create']).' ';
            echo Html::a('<i class="fa fa-table" aria-hidden="true"></i> '.Yii::t('ezform', 'View Custom Table'), Url::to(['/ezforms2/ezform/view']), ['class' => 'btn btn-info']);
        ?>
    </div>
        
  </div>
    
    <?php
    \backend\modules\ezforms2\classes\EzfStarterWidget::begin(['modal_ezform'=>false]);
    Pjax::begin(['id' => 'ezform-grid-pjax', 'timeout' => FALSE]); ?>
    <?php
    //echo $this->render('search', ['model' => $searchModel, 'tab' => $tab]);
    echo GridView::widget([
        'id' => 'ezform-grid',
        //'panel' => false,
        'spacBottom'=> true,
        'panelBtn' => $this->render('search', ['model' => $searchModel, 'tab' => $tab]),
        'dataProvider' => $dataProvider,
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
            'ezf_detail:ntext',
            'ezf_version',
            [
                'attribute' => 'fullname',
                'label' => Yii::t('ezform', 'Created By')
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y'],
                'contentOptions' => ['style' => 'width:100px;text-align: center;'],
            ],
            
            [
                'class' => 'backend\modules\ezforms2\classes\ActionColumn',
                'template' => $items[$tab - 1]['template'],
                'buttons' => [
                    'datalist' => function ($url, $data, $key) {
                        $dbConfig =  explode('=', \Yii::$app->db->dsn);
                        $dbname = isset($dbConfig[2])?$dbConfig[2]:'';
                    
                        //$dbConfig =  explode('=', \Yii::$app->db->dsn);
                        //$dbname = isset($dbConfig[2])?$dbConfig[2]:'';
                        $cascapReport = isset(Yii::$app->params['cascapReport']) ? Yii::$app->params['cascapReport'] : "https://report.cascap.in.th/exportdata/request.php?scheme=";
                        
                        $html = '<div class="btn-group ">
                                <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.Yii::t('ezform', 'Data Mgt').' <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>'.Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('ezform', 'EzEntry'), Url::to(['/ezforms2/data-lists/index',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'datalist',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>
                                    <li>'.backend\modules\ezforms2\classes\EzfHelper::btn($data->ezf_id)
                                        ->tag('a')
                                        ->options(['class'=>'', 'data-action' => 'datalist', 'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0'])
                                        ->label('<i class="fa fa-database"></i> '.Yii::t('ezform', 'Data Table'))
                                        ->buildBtnGrid().
                                    '</li>
                                    <li class="disabled">'.Html::a('<span class="fa fa-check"></span> '.Yii::t('ezform', 'Data Validation'), '#', [
                                    'data-action' => 'datalist',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>    
                                    <li class="disabled">'.Html::a('<span class="fa fa-dashboard"></span> '.Yii::t('ezform', 'Data Analysis'), '#', [
                                    'data-action' => 'datalist',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li> 
                                    <li class="">'.Html::a('<span class="glyphicon glyphicon-tint"></span> '.Yii::t('ezform', 'Purify'), ['/purify/table?ezf_id='.$data->ezf_id], [
                                    'data-action' => 'datalist',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>  
                                    <li>'.Html::a('<span class="glyphicon glyphicon-save-file"></span> '.Yii::t('ezform', '.RData (Program Rstudio)'),
                                        "{$cascapReport}".$dbname.'&ezf_id='.$data->ezf_id.'&typefile=rdata', [
                                    ]).'</li>
                                    <li>'.Html::a('<span class="glyphicon glyphicon-cloud-download"></span> '.Yii::t('ezform', '.dta (Program Stata)'),
                                        "{$cascapReport}".$dbname.'&ezf_id='.$data->ezf_id.'&typefile=stata', [
                                    ]).'</li>
                                 </ul>
                                  </div>
                                ';
                        
                        return $html;
                    },
                            
                    'annotated' => function ($url, $data, $key) {
                        return EzfHelper::btn($data->ezf_id)->options([
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'annotated',
                                'title' => Yii::t('ezform', 'Annotated CRF'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnAnnotated();
                    },
                    'er' => function ($url, $data, $key) {
                        $html = '<div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-sitemap"></span> RD <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>'. Html::a('<span class="fa fa-sitemap"></span> ERD', Url::to(['/ezforms2/ezform/er',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'er',
                                    'title' => Yii::t('ezform', 'Entity Relation Diagram'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn-er',
                            ]) .'</li>
                                    <li>'.Html::a('<span class="fa fa-sitemap"></span> SRD', Url::to(['/ezforms2/ezform/srd',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'er',
                                    'title' => Yii::t('ezform', 'Staple Relation Diagram'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn-er',
                            ]).'</li>
                                     
                                  </ul>
                                  </div>
                                ';
                        
                        return $html;
                        
                        return Html::a('<span class="fa fa-sitemap"></span> ERD', Url::to(['/ezforms2/ezform/er',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'er',
                                    'title' => Yii::t('ezform', 'Entity Relationship Diagram'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-default btn-xs btn-er',
                            ]);
                    },  
                            
                    'dictionary' => function ($url, $data, $key) {
                        return EzfHelper::btn($data->ezf_id)->options([
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'dictionary',
                                'title' => Yii::t('ezform', 'Dictionary'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnDictionary();
                    },
                    'meta' => function ($url, $data, $key) {
                        $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.Yii::t('ezform', 'Meta Data').' <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>'.EzfHelper::btn($data->ezf_id)->tag('a')->options([
                                'class' => '',
                                'data-action' => 'annotated',
                                'title' => Yii::t('ezform', 'Annotated CRF'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnAnnotated().'</li>
                                    <li>'.EzfHelper::btn($data->ezf_id)->tag('a')->options([
                                'class' => '',
                                'data-action' => 'dictionary',
                                'title' => Yii::t('ezform', 'Dictionary'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ])->buildBtnDictionary().'</li>
                                     
                                  </ul>
                                  </div>
                                ';
                        
                        return $html;
                    },        
                    'export' => function ($url, $data, $key) {
                        $html = '<div class="btn-group">
                                <button type="button" class="btn btn-warning btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.Yii::t('ezform', 'Form Mgt').' <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>'.Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('ezform', 'Backup Data'), Url::to(['/ezforms2/data-lists/export',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'export',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>
                                    <li>'.Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('ezform', 'Backup EzForm'), Url::to(['/ezbuilder/ezform-builder/export',
                                                'ezf_id' => $data->ezf_id,
                                                'v' => $data->ezf_version,
                                            ]), [
                                        'data-action' => 'export',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>
                                    <li>'.Html::a('<span class="glyphicon glyphicon-save-file"></span> '.Yii::t('ezform', 'Restore Data'), Url::to(['/ezbuilder/ezform-builder/excel2zdata',
                                                'ezf_id' => $data->ezf_id,
                                                'v' => $data->ezf_version,
                                            ]), [
                                        'data-action' => 'link',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>
                                    <li>'.Html::a('<span class="fa fa-clone"></span> '.Yii::t('ezform', 'Clone'), Url::to(['/ezforms2/ezform/clone',
                                                'ezf_id' => $data->ezf_id,
                                            ]), [
                                        'data-action' => 'clone',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    ]).'</li>    
                                  </ul>
                                  </div>
                                ';
                        
                        return $html;
                    },
                            
                    'clone' => function ($url, $data, $key) {
                        $options = appxq\sdii\utils\SDUtility::string2Array($data['ezf_options']);
                        if(isset($options['allowed_clone']) && $options['allowed_clone']==1){
                            return Html::a('<span class="fa fa-clone"></span> '.Yii::t('ezform', 'Clone'), Url::to(['/ezforms2/ezform/clone',
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => 'clone',
                                    'title' => Yii::t('ezform', 'Clone'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-info btn-xs',
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'token' => function ($url, $data, $key) {
                        $options = \appxq\sdii\utils\SDUtility::string2Array($data->ezf_options);
                        $btn = 'default';
                        $icon = 'cloud-upload';
                        $label = Yii::t('ezform', 'Create Token');
                        $action = 'token';
                        if(isset($options['token'])){
                            $btn = 'warning';
                            $label = Yii::t('ezform', 'Manage Token');
                            $icon = 'cog';
                            $action = 'manage-token';
                        }
                        return Html::a('<span class="fa fa-'.$icon.'"></span> '.$label, Url::to(['/ezforms2/ezform/'.$action,
                                            'ezf_id' => $data->ezf_id,
                                        ]), [
                                    'data-action' => $action,
                                    'title' => $label,
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => "btn btn-$btn btn-xs",
                        ]);
                    },        
                ],
                'contentOptions' => ['style' => 'width:560px;text-align: center;'],
            ]
        ],
    ]);
    ?>
    <?php Pjax::end();
 
 
 \backend\modules\ezforms2\classes\EzfStarterWidget::end();
    ?>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-create',
    'size' => 'modal-xxl',
]);
?>
<?=
ModalForm::widget([
    'id' => 'modal-ezform',
    'size' => 'modal-lg',
]);
?>

<?php 
$dblclick = '';
if($tab==1 || $tab==2){
    $dblclick = "$('#ezform-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    location.href='" . Url::to(['/ezbuilder/ezform-builder/update', 'id' => '']) . "'+id;
});";
}

$this->registerJs("
$('#modal-addbtn-create').on('click', function() {
    var url = $(this).attr('data-url');
    modalCreate(url);
});

$('#modal-create').on('click', '.btn-back', function() {
    $('#modal-create #view-method').show();
    $('#modal-create #view-form').hide();
});

$('#modal-create').on('click', '#modal-step-ezform', function() {
    var url = $(this).attr('data-url');
    $('#modal-create #view-method').hide();
    $('#modal-create #view-form').show();
    $('#modal-create #view-form .content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        
    $.ajax({
	url:url,
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#modal-create #view-form .content').html(result);
	}
    });
});

$('#modal-create').on('click', '#modal-addbtn-ezform', function() {
    var url = $(this).attr('data-url');
    
    $('#modal-ezform .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    
    $('body').waitMe({
            effect : 'facebook',
            text : '".Yii::t('ezform', 'Please wait...')."',
            bg : 'rgba(0,0,0,0.7)',
            color : '#FFF',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '20px',
            source : '',
            onClose : function() {
                //$('#btn-line').trigger('click');
            }
        });
        
    $.ajax({
	method: 'GET',
        data:{auto:1},
	url:url,
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#modal-ezform .modal-content').html(result);
	}
    });
});

$dblclick	

$('#modal-create').on('click', '.btn-tmp', function() {
    var url = $(this).attr('href');
    
    $('body').waitMe({
            effect : 'facebook',
            text : '".Yii::t('ezform', 'Please wait...')."',
            bg : 'rgba(0,0,0,0.7)',
            color : '#FFF',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '20px',
            source : '',
            onClose : function() {
                //$('#btn-line').trigger('click');
            }
        });
        
    $.ajax({
            url:url,
            dataType: 'JSON',
            success: function(result, textStatus) {
                if(result.status == 'success') {
                    $('#modal-create #view-method').hide();
                    $('#modal-create #view-form').show();
                    $.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
                    $('#modal-create #view-form .content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');

                    $.ajax({
                        url:'".Url::to(['/ezforms2/ezform/save'])."',
                        method: 'GET',
                        data:{id:result.id},
                        dataType: 'HTML',
                        success: function(result, textStatus) {
                            $('#modal-create #view-form .content').html(result);
                        }
                    });
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                } 
                $('body').waitMe('hide');
            }
        });
        
    return false;
});

$('#ezform-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	//modalEzform(url);
        location.href=url;
        return false;
    } else if(action === 'clone') {
        yii.confirm('".Yii::t('ezform', 'Are you sure you want to clone this item?')."', function() {
            location.href=url;
        });
        return false;
    } else if(action === 'token') {
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                $.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
        return false;
    } else if(action === 'manage-token') {
        modalEzform(url);
        return false;
    } else if(action === 'delete') {
        var txtConfirm = $(this).attr('data-confirm');
	yii.confirm(txtConfirm, function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    $.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
        return false;
    } else if(action === 'er') {
        modalEzformInfo(url);
        return false;
    }
    
});

function modalEzform(url) {
    $('#modal-ezform .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform').modal('show')
    .find('.modal-content')
    .load(url);
}

function modalCreate(url) {
    $('#modal-create .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-create').modal('show')
    .find('.modal-content')
    .load(url);
}
function modalEzformInfo(url) {
    $('#modal-ezform-info .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-info').modal('show')
    .find('.modal-content')
    .load(url);
}

"); ?>