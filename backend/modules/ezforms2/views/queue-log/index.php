<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\grid\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use yii\web\JsExpression;
use backend\modules\ezforms2\classes\EzfQuery;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\QueueLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title =  Yii::t('ezform', 'EzWorkBench');
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="queue-log-index">
<?php  Pjax::begin(['id'=>'queue-log-grid-pjax']);?>
  <?php
  
  $target = isset($_GET['target'])?$_GET['target']:'';
$workingList = \backend\modules\ezforms2\classes\EzfList::getWorkingUnit();
$userProfile = Yii::$app->user->identity->profile;
$dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : 0;

  ?>
  <div class="sdbox-header" style="margin-bottom: 10px;">
	
        
        <div style="width: 250px;"class="pull-right" >
        <?=  kartik\select2\Select2::widget([
        'id'=>'working-list-ezwb',
        'name' => 'ezwb',
        'value'=>$dept,
        'data' => $workingList,
        'options' => ['placeholder' => Yii::t('ezform', 'Select Working ...')],
        'pluginOptions' => [
        ]
    ])?>
          </div>
    <?php
    /** @var dektrium\user\models\User $user */
    $username = 'Guest';
    $uicon = '';
    $avatar_url = Yii::getAlias('@storageUrl') . '/images/nouser.png';
    if (!Yii::$app->user->isGuest){
        $user = Yii::$app->user->identity;
        
        $sql = "SELECT id AS `code`, CONCAT(unit_code, ' : ', unit_name) AS `name` FROM zdata_working_unit zwu WHERE `id`=:id AND zwu.rstat not in(0,3)";
        $data = Yii::$app->db->createCommand($sql, [':id'=>$user->profile->department])->queryOne();
        $unit_name = '';
        if($data){
            $unit_name = $data['name'];
        }
        $username = (isset($user->profile->user_id)) ?$user->profile->firstname . ' ' . $user->profile->lastname.' ['.$unit_name.']': 'Guest';

        if(isset($user->profile->avatar_path) && !empty($user->profile->avatar_path)){
            $avatar_url = $user->profile->avatar_base_url.'/'.$user->profile->avatar_path;
        }
        $uicon = '<img class="img-circle" src="'.$avatar_url.'" height="30"/>';
    } else {
        $uicon = '<img class="img-circle" src="'.$avatar_url.'" height="30"/>';
    }
    $user_title = $uicon . ' '.$username;
    ?>
     
    <h3><?=  Html::encode($this->title) ?> : <?=$user_title?></h3>
    </div>

  
  
  <?php
  
//count display
$created_at = isset($searchModel['created_at'])?$searchModel['created_at']:'';
$countIncomming = EzfQuery::countIncomming($created_at);
$countInprocess = EzfQuery::countInprocess($created_at);
$countIncompleted = EzfQuery::countIncompleted($created_at);
$countOutgoing = EzfQuery::countOutgoing($created_at);
  ?>
  <?php EzfStarterWidget::begin(); ?>
  
  <div class="row" style="margin-bottom: 10px; ">
      <div class="col-md-12">
        <a class="btn btn-success " href="<?= Url::to(['/ezmodules/ezmodule/view', 'id'=>1550216282096851900])?>"  > HIS</a>
        <a class="btn btn-default " href="<?= Url::to(['/ezforms2/queue-log/index'])?>"  > All Tasks</a>
        <a class="btn btn-default " href="<?= Url::to(['/ezforms2/queue-log/index'])?>"  disabled> My Tasks</a>
        <a class="btn btn-info ezform-main-open" data-modal="modal-ezform-main" data-url="/ezforms2/ezform/organize-chart"><span class="fa fa-sitemap"></span> Organize Chart</a>
    </div>
  </div>
  
  <ul class="nav nav-tabs" style="margin-bottom: 10px;">
    <li role="presentation" class="<?=$tab==''?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'created_at'=>$created_at])?>"><i class="glyphicon glyphicon-log-in"></i> In-Comming <span class="badge badge-value" style="background-color: #d9534f;"><?=$countIncomming?></span></a></li>
  <li role="presentation" class="<?=$tab=='process'?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'process', 'created_at'=>$created_at])?>"><i class="fa fa-cog fa-spin"></i> In-Process <span class="badge badge-value" style="background-color: #d9534f;"><?=$countInprocess?></span></a></li>
  <li role="presentation" class="<?=$tab=='out'?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'out', 'created_at'=>$created_at])?>"><i class="glyphicon glyphicon-log-out"></i> Out-Going <span class="badge badge-value" style="background-color: #d9534f;"><?=$countOutgoing?></span></a></li>
  <li role="presentation" class="<?=$tab=='completed'?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'completed', 'created_at'=>$created_at])?>"><i class="glyphicon glyphicon-check"></i> Completed <span class="badge badge-value" style="background-color: #d9534f;"><?=$countIncompleted?></span></a></li>
  <li role="presentation" class="<?=$tab=='report'?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'report'])?>"><i class="fa fa-area-chart"></i> Reports</a></li>
  <li role="presentation" class="<?=$tab=='webboard'?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'webboard'])?>"><i class="fa fa-comment"></i> Webboard</a></li>
</ul>
  
  
  
  <?php
  //$btnR = \backend\modules\ezforms2\classes\EzfQuery::getEzWorkBtn($dept);
  
  ?>
    <div style="margin-bottom: 10px;">
      <div class="row">
            <div class="col-md-3 ">
            <?php
        echo kartik\select2\Select2::widget([
            'name' => 'ezf-select-WorkBench',
            'value'=> '',
            'options' => ['placeholder' => 'Select My Favorite forms', 'id'=>'ezf-select-WorkBench'],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/ezform/get-favorite-forms']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                        if(jqXHR.status&&jqXHR.status==403){
                            window.location.href = "'.Url::to(['/user/login']).'"
                        }
                    }'),
                ],
            ],
            'pluginEvents' => [
                "select2:select" => "function(e) { 
                    let ezf_id = e.params.data.id;
                    let url = '".Url::to(['/ezforms2/ezform-data/ezform', 'target'=>$target, 'reloadPage'=> base64_encode(Url::current()), 'modal'=>'modal-ezform-main', 'popup'=>1, 'ezf_id'=>''])."'+ezf_id;
                        
                    $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-ezform-main').modal('show')
                    .find('.modal-content')
                    .load(url);

                   $('#ezf-select-WorkBench').val('');
                   $('#ezf-select-WorkBench').trigger('change');
                }",
            ]
        ]);
        ?> 
              
            </div>
        <div class="col-md-3 sdbox-col" >
          <?php
        echo kartik\select2\Select2::widget([
            'name' => 'ezf-select-outgoing',
            'value'=> '',
            'options' => ['placeholder' => 'Select Out-Going task', 'id'=>'ezf-select-outgoing'],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/ezform/get-ezunit-forms', 'dept'=>$dept]),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                        if(jqXHR.status&&jqXHR.status==403){
                            window.location.href = "'.Url::to(['/user/login']).'"
                        }
                    }'),
                ],
            ],
            'pluginEvents' => [
                "select2:select" => "function(e) { 
                    let ezf_id = e.params.data.id;
                    let url = '".Url::to(['/ezforms2/ezform-data/ezform', 'target'=>$target, 'reloadPage'=> base64_encode(Url::current()), 'modal'=>'modal-ezform-main', 'popup'=>1, 'ezf_id'=>''])."'+ezf_id;
                        
                    $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-ezform-main').modal('show')
                    .find('.modal-content')
                    .load(url);

                   $('#ezf-select-outgoing').val('');
                   $('#ezf-select-outgoing').trigger('change');
                }",
            ]
        ]);
        ?>
                <?php
      //echo \backend\modules\ezforms2\classes\EzfFunc::renderBtnWork($btnR);
        //echo \backend\modules\ezforms2\classes\EzfFunc::renderBtnWork($btnS);
      
      //Select Out-Going task
      ?>
            
            
        </div>
          <div class="col-md-6 sdbox-col" >
            <a class="btn btn-success"  href="<?= Url::to(['/ezmodules/ezmodule/view', 'id'=>'1556635841010430100'])?>"><i class="glyphicon glyphicon-check"></i> Project Plan</a>
            <button class="btn btn-info" data-toggle="tooltip"  disabled><i class="glyphicon glyphicon-heart"></i> Care Plan</button>
            <button class="btn btn-info btn-controller" data-toggle="modal" data-target="#controller"><i class="fa fa-gamepad"></i> Controller</button>
            <a class="btn btn-default " href="<?= Url::to(['/ezmodules/ezmodule/view', 'id'=>1555659776048629700])?>"  > <i class="glyphicon glyphicon-calendar"></i> Calendar</a>
            <button class="btn btn-warning ezform-main-open" data-modal="modal-ezform-main" data-url="/ezforms2/ezform-data/view?ezf_id=1515477110090743900&modal=modal-ezform-main&popup=1" data-toggle="tooltip" ><i class="fa fa-magic" aria-hidden="true"></i> Unit management</button>
            <button class="btn btn-primary ezform-main-open" data-modal="modal-ezform-main" data-url="/ezforms2/ezform-data/view?ezf_id=1515479766054620300&modal=modal-ezform-main&popup=1" data-toggle="tooltip" ><i class="glyphicon glyphicon-cog"></i> Unit Settings</button>
        </div>
        
        <div class="modal fade" id="controller" tabindex="-1" role="dialog" aria-labelledby="controller">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Pre-defined, automated task controllers</h4>
              </div>
              <div class="modal-body">
                
                <button class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Create Controller</button>
                <table class="table table-striped"> 
                  <thead> <tr> <th>#</th> <th>Controller Name</th> <th>Creators</th> <th>Date Created</th> <th>Type</th> </tr> </thead> 
                  <tbody> 
                    <tr> <th scope="row">1</th> <td>Controller Test</td> <td>Admin</td> <td>30/04/2019</td> <th>ประเภทบริการ</th></tr> 
                    
                  </tbody> 
                </table>
                
              </div>
            </div>
          </div>
        </div>
        
    </div>
      
      
      
      
  </div>
  
    <?= GridView::widget([
	'id' => 'queue-log-grid',
//	'panelBtn' => 
//		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['queue-log/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-queue-log', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
//	    [
//		'class' => 'yii\grid\CheckboxColumn',
//		'checkboxOptions' => [
//		    'class' => 'selectionQueueLogIds'
//		],
//		'headerOptions' => ['style'=>'text-align: center;'],
//		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
//	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],
            [
		'attribute'=>'suser_name',
                'label'=>'Sender Name',
		'contentOptions'=>['style'=>'width:120px; '],
            ],
            [
		'attribute'=>'sunit_name',
                'label'=>$tab=='out'?'Target Unit':'Sender Unit',
		'contentOptions'=>['style'=>'width:120px; '],
                'filter'=>kartik\select2\Select2::widget([
                    'model'=>$searchModel,
                    'attribute'=>'current_unit',
                    'data' => $workingList,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select Unit ...')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],  
            
            [
		'attribute'=>'tab_name',
                'label'=>'Objective -> Form Menu',
                'format'=>'raw',
                'value'=>function ($data){ 
                        $detail = appxq\sdii\utils\SDUtility::string2Array($data['field_detail']);
                        $actions = \backend\modules\ezforms2\classes\EzfHelper::btn($data['ezf_id'])
                            ->label('<i class="glyphicon glyphicon-eye-open"></i> View ->'.$data['tab_name'])
                            ->options(['class'=>'btn btn-info btn-xs'])
                            ->buildBtnView($data['dataid']);
                        
                        if (!empty($detail)) {
                            try {
                                $query = new \yii\db\Query();
                                $query->select(['*']);
                                $query->from($data['ezf_table']);
                                $query->where('id=:id', [':id' => $data['dataid']]);

                                $zdata = $query->createCommand()->queryOne();
                            } catch (\yii\db\Exception $e) {
                                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                return NULL;
                            }
                            
                            if($zdata){
                                $ezf_id = isset($data['ezf_id'])?$data['ezf_id']:0;
                                $modelEzf = EzfQuery::getEzformOne($ezf_id);
                                
                                
                                $options = appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);
                                $html = '<div class="pull-right text-right">'.$actions.'</div>  <h4 class="page-header" style="margin-top: 0px; margin-bottom: 10px;">'.$modelEzf['ezf_name'].'</h4>';
                                $comma = '';
                                $column = isset($options['display_col'])?$options['display_col']:0;
                                $template = isset($options['display_tmp'])?$options['display_tmp']:'<span class="content_box"><b class="content_label">{label}: </b><span class="content_value">{value}</span> </span>';
                                $row_count = 0;
                                $field_count = count($detail);
                                foreach ($detail as $index_field => $field) {
                                    $modelFields = EzfQuery::getFieldAllVersion($data['ezf_id']);
                                    $col = $column>0?12/$column:0;
                                    $sdbox = '';
                                    $row_count++;


                                    if($col>0 && $row_count>1){
                                        $sdbox = 'sdbox-col';
                                    }

                                    $template_content = $template;
                                    if($col>0) {
                                        $template_content = "<div class=\"col-md-$col $sdbox\">{$template}</div>";
                                    }

                                    if($modelFields){
                                        foreach ($modelFields as $key => $value) {
                                            $var = $value['ezf_field_name'];
                                            $version = $value['ezf_version'];
                                            if($field == $var && ($zdata['ezf_version'] == $version || $version=='all')){
                                                $dataInput;
                                                if (Yii::$app->session['ezf_input']) {
                                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                                                }
                                                if(isset($zdata[$var]) && $zdata[$var]!=''){
                                                    if($col>0 && $row_count==1){
                                                        $html .= '<div class="row" >';
                                                    }
                                                    $html .= strtr($template_content, [
                                                        '{label}' => $value['ezf_field_label'],
                                                        '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata),
                                                    ]);
                                                            //$comma . "<b>{$value['ezf_field_label']}</b>" . backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata);
                                                } else {
                                                    $row_count--;
                                                }
                                                break;
                                            }
                                            $comma = ' ';
                                        }
                                    }

                                    if($col>0 && ($row_count==$column || $field_count==$index_field+1)){
                                        $html .= '</div>';
                                        $row_count = 0;
                                    }
                                }
                                return $html;
                            }
                        }
                        
                    return  $actions;
                        
                                    },
		//'contentOptions'=>['style'=>'width:180px; '],
            ],
            
            [
		'attribute'=>'created_at',
                'label'=>'Date Created',
		'value'=>function ($data){ return appxq\sdii\utils\SDdate::mysql2phpDateTime($data['created_at']); },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'min-width:100px;width:100px; text-align: center;'],
                        'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'created_at',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'dr_workb_list', 'class'=>'form-control'],
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'d-m-Y',
                                'separator'=>' to ',
                                //'language'=>'TH',
                            ],
                            //'opens'=>'left'
                        ]
                    ]),
            ],
                        
            [
		'attribute'=>'created_at',
                'label'=>'Duration',
		'value'=>function ($data){ return appxq\sdii\utils\SDdate::differenceTimer($data['created_at']); },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:120px; text-align: center;'],
                'filter'=>false
            ],
                        
            [
		'header'=>'Action',
                'format'=>'raw',
		'value'=>function ($data){ 
                    $process_forms = appxq\sdii\utils\SDUtility::string2Array($data['process_forms']);
                    if($data['type'] == 'send'){
                        $field = EzfQuery::getFieldById($data['setting_id']);
                        if($field){
                            $optons = appxq\sdii\utils\SDUtility::string2Array($field['ezf_field_options']);
                            $process_forms = isset($optons['options']['process_form'])?$optons['options']['process_form']:[];
                        }
                    }
                    $formList = [];
                    $li = '';
                    $html_status = '';
                    $ezform_array = [];
                    foreach ($process_forms as $form_id) {
                        $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($form_id);
                        $formList[$form_id] = $modelEzf->ezf_name;
                        $dataid = '';
                        if($data['status']=='completed'){
                            $dataid_receive = appxq\sdii\utils\SDUtility::string2Array($data['dataid_receive']);
                            if(isset($dataid_receive[$form_id])){
                                $dataid = '&amp;dataid='.$dataid_receive[$form_id];
                                $html_status = '<i class="fa fa-check" aria-hidden="true"></i>';
                            }
                        }
                        $li .= "<li><a class=\"ezform-main-open\" data-modal=\"modal-ezform-main\" data-url=\"/ezforms2/ezform-data/ezform?ezf_id={$form_id}{$dataid}&amp;modal=modal-ezform-main&amp;reloadDiv=&amp;initdata=&amp;target={$data['dataid']}&amp;popup=1\">{$html_status} {$modelEzf->ezf_name}</a></li>";
                    }
                    
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           Forms <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          '.$li.'
                        </ul>
                      </div>';
                    
                    if(empty($process_forms)){
                        $html = '';
                    }
                    
//                    $li_receive = '';
//                    $receive_forms = appxq\sdii\utils\SDUtility::string2Array($data['dataid_receive']);
//                    foreach ($receive_forms as $key_rf => $value_rf) {
//                        $li_receive .= "<li><a class=\"ezform-main-open\" data-modal=\"modal-ezform-main\" data-url=\"/ezforms2/ezform-data/ezform?ezf_id={$key_rf}&amp;dataid={$value_rf}&amp;modal=modal-ezform-main&amp;reloadDiv=&amp;initdata=&amp;target={$data['dataid']}&amp;popup=1\">{$formList[$key_rf]}</a></li>";
//                    }
//                    $html_receive = '<div class="btn-group">
//                        <button type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//                           Receive <span class="caret"></span>
//                        </button>
//                        <ul class="dropdown-menu">
//                          '.$li_receive.'
//                        </ul>
//                      </div>';
//                    
//                    if(empty($receive_forms)){
//                        $html_receive = '';
//                    }
                    
                    return $html.' '.(($data['module_id']>0)?Html::a('<i class="fa fa-cube"></i> '.$data['module_name'], Url::to(['/ezmodules/ezmodule/view', 'id'=>$data['module_id'], 'target'=>$data['dataid']]), ['class'=>'btn btn-warning btn-xs']):''); 
                },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:80px; text-align: center;'],
            ],            
               
            [
		'attribute'=>'status',
		'value'=>function ($data){ 
                    $items = ['in_comming'=>'In Comming', 'process'=>'Process', 'completed'=>'Completed'];
                    return $items[$data['status']]; 
                    
                },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                'filter'=> Html::activeDropDownList($searchModel, 'status', ['in_comming'=>'In Comming', 'process'=>'Process', 'completed'=>'Completed'], ['class'=>'form-control', 'prompt'=>'All'])
            ],
                        
//            'unit',
//            'ezf_id',
//            'dataid',
//            'status',
            // 'enable',
            // 'setting_id',
            // 'module_id',
            // 'current_unit',
            // 'user_receive',
            // 'time_receive',
            // 'options:ntext',
            // 'updated_by',
            // 'updated_at',
            // 'created_by',
            // 'created_at',
            // 
                        //complete_cond
	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{completed} {delete}',
                'buttons' => [
                    'completed' => function ($url, $model, $key) {
                        if(isset($model->complete_cond) && $model->complete_cond=='2'){
                            return Html::a('<span class="glyphicon glyphicon-ok"></span> '.Yii::t('yii', 'Completed'), Url::to(['/ezforms2/queue-log/completed', 'id'=>$model->id]), [
                                        'class'=>'btn btn-primary btn-xs',
                                        'style'=>"margin-bottom: 5px;",
                                        'data-action' => 'completed',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to completed this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
				]);
                        } else {
                            return '';
                        }
				
                    },
                    'delete' => function ($url, $model, $key) {
                            if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('yii', 'Delete'), $url, [
                                    'class'=>'btn btn-danger btn-xs',
                                    'style'=>"margin-bottom: 5px;",
                                    'data-action' => 'delete',
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
				]);
                            }
                            return '';
                                
			},
                ],
	    ],
        ],
    ]); ?>
  <?php EzfStarterWidget::end(); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-queue-log',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#queue-log-grid-pjax').on('change', '#working-list-ezwb', function(){
    $.ajax({
        method: 'POST',
        url: '".Url::to(['queue-log/change-unit'])."',
        data: {unit:$(this).val()},
        dataType: 'JSON',
        success: function(result, textStatus) {
            if(result.status == 'success') {
                ". SDNoty::show('result.message', 'result.status') ."
                $.pjax.reload({container:'#queue-log-grid-pjax'});
            } else {
                ". SDNoty::show('result.message', 'result.status') ."
            }
        }
    });
});

$('#queue-log-grid-pjax').on('click', '#modal-addbtn-queue-log', function() {
    modalQueueLog($(this).attr('data-url'));
});

$('#queue-log-grid-pjax').on('click', '#modal-delbtn-queue-log', function() {
    selectionQueueLogGrid($(this).attr('data-url'));
});

$('#queue-log-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#queue-log-grid').yiiGridView('getSelectedRows');
	disabledQueueLogBtn(key.length);
    },100);
});

$('#queue-log-grid-pjax').on('click', '.selectionQueueLogIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledQueueLogBtn(key.length);
});

$('#queue-log-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalQueueLog(url);
        return false;
    } else if(action === 'completed') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to completed this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#queue-log-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
        return false;
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#queue-log-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
        return false;
    }
    
});

function disabledQueueLogBtn(num) {
    if(num>0) {
	$('#modal-delbtn-queue-log').attr('disabled', false);
    } else {
	$('#modal-delbtn-queue-log').attr('disabled', true);
    }
}

function selectionQueueLogGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionQueueLogIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#queue-log-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

$('body').on('hidden.bs.modal', '#modal-ezform-main', function(e){
    $.pjax.reload({container:'#queue-log-grid-pjax'});
});


function modalQueueLog(url) {
    $('#modal-queue-log .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-queue-log').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>
<?php \appxq\sdii\widgets\CSSRegister::begin([
    //'key' => 'bootstrap-modal',
    //'position' => []
]); ?>
<style>
    /*CSS script*/
.content_box .content_value{
    color: rgb(243, 121, 52);
}
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>