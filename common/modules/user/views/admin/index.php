<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */
$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;

$dataAdminsite = \common\modules\user\models\User::getNotAdminsite();
$projectData = \cpn\chanpan\classes\utils\CNProject::getMyProject();
$projectData = $projectData['data_create'];

?>
<hr /> 
<div class="col-md-10">
    <?php
    yii\bootstrap\ActiveForm::begin([
            'options' => [
                'id' => 'frmManagerProject'
    ]])
    ?>	
    <div class="form-group">
        <div class="row">
            <label class="col-sm-1 col-md-1 col-xs-1 col-lg-1 text-right"><?= Yii::t('chanpan', 'Search') ?>: </label>
            <div class="col-sm-3 col-md-3 col-xs-3 col-lg-3 text-right" style="padding: 0;">
                <input type="text" id="txtSearch" class="form-control" name="txtSearch" placeholder="<?= Yii::t('chanpan', 'Search for Name , Site') ?>">
            </div>
            <div class="col-sm-3 col-md-3 col-xs-3 col-lg-3 filter-date">
                <div class="input-group">
                    <span class="input-group-addon kv-date-calendar" title="Select date">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </span>
                    <?php
                    echo yii\jui\DatePicker::widget([
                        'id' => 'fromdate',
                        'name' => 'fromdate',
                        'language' => 'en',
                        'dateFormat' => 'yyyy-MM-dd',
                        'value' => date('Y-m-01'),
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                        ],
                        'clientOptions' => [
                            'defaultDate' => Yii::$app->formatter->asDate('now', 'php:Y-m-01'),
                            'maxDate' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
                        ],
                    ]);
                    ?>
                </div>
            </div>
                <div class="col-sm-3 col-md-3 col-xs-3 col-lg-3 filter-date">
                    <div class="input-group">
                        <span class="input-group-addon kv-date-calendar" title="Select date">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </span>
                        <?php
                        echo yii\jui\DatePicker::widget([
                            'id' => 'todate',
                            'name' => 'todate',
                            'language' => 'en',
                            'dateFormat' => 'yyyy-MM-dd',
                            'value' => date('Y-m-d'),
                            'options' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],
                            'clientOptions' => [
                                'defaultDate' => Yii::$app->formatter->asDate('now', 'php:Y-m-01'),
                            ],
                        ]);
                        ?>
                    </div>
                </div>
            <div class="col-sm-2 text-left" style="padding-left: 5px;">
                <div class="btn-group">
                    <input name="filter" id="filter" value="hide" type="hidden">
                    <button type="button" class="btn btn-default" id="btnSetting" data-toggle='tooltip' title="Filter by Last login"><i class="fa fa-cogs"></i></button>
                    <button type="submit" class="btn btn-primary" id="btnSearch"><i class="fa fa-search"></i> <?= Yii::t('chapan', 'Search') ?></button>
                </div>
            </div>
            </div>

        </div>
    </div>    

<?php yii\bootstrap\ActiveForm::end(); ?>
</div>    

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'tableOptions'=>['id'=>'gridUser', 'class'=>'table table-bordered table-responsive table-hover'],
    'layout' => "{items}\n{pager}",
    'columns' => [
        [
           'class' => 'yii\grid\CheckboxColumn',  
            'header' => Html::button("<i class='fa fa-remove'></i>", [
                'disabled'=>true,
                'class'=>'btn btn-danger',
                'id'=>'btnDeleteAll',
                'title'=>Yii::t('chanpan','Delete user select')
            ]),
            'headerOptions' => ['style' => 'text-align: center;width:50px;'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => Yii::t('chanpan', 'Number'),
        ],
        [
            'attribute' => 'firstname',
            'label' => Yii::t('chanpan', 'First name'),
            'value' => 'profile.firstname'
        ],
        [
            'attribute' => 'lastname',
            'label' => Yii::t('chanpan', 'Last name'),
            'value' => 'profile.lastname'
        ],
        [
            'attribute' => 'sitecode',
            'label' => Yii::t('chanpan', 'Sitecode'),
            'value' => function ($model) {
                if (!empty($model->profile->sitecode)) {
                    $sitecode = isset($model->profile->sitecode) ? $model->profile->sitecode : '';
                    return \common\modules\user\classes\SiteCodeFunc::getSiteCodeValue($sitecode);
                } else {
                    return ' ';
                }
            },
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:90px;text-align: center;'],
        ],
        [
            'header' => Yii::t('chanpan', 'Admin'),
            'value' => function ($model) use ($dataAdminsite) {
                $data = \common\modules\user\classes\CNAuth::canAdmin($model->id);

                //$sitecode = isset($model->profile->sitecode) ? $model->profile->sitecode : '';

                if (!empty($data)) {
                    return '<i style="color:green;" class="glyphicon glyphicon-ok"></i>';
                }
                return '<i style="color:red;" class="glyphicon glyphicon-remove-sign"></i>';
            },
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:90px;text-align: center;'],
        ],
        [
            'header' => Yii::t('user', 'Admin Site'),
            'value' => function ($model) use ($projectData) {
                $auth = Yii::$app->authManager->getAssignment('adminsite', $model->id);
                if($projectData['user_create'] == $model->id){
                    return "Owner";
                }
                if (isset($auth->roleName)) {
                    return Html::button('<i class="glyphicon glyphicon-ok"></i>', [
                                'class' => 'manager-btn btn btn-xs btn-primary',
                                'data-id' => $model->id,
                                'data-action'=>'admin',
                                'data-url' => yii\helpers\Url::to(['manager', 'id' => $model->id, 'auth' => 'adminsite'])
                    ]);
                } else {
                    return Html::button('<i class="glyphicon " style="padding-right: 6px; padding-left: 6px;"></i>', [
                                'class' => 'manager-btn btn btn-xs btn-default',
                                'data-id' => $model->id,
                                'data-action'=>'admin',
                                'data-url' => yii\helpers\Url::to(['manager', 'id' => $model->id, 'auth' => 'adminsite'])
                    ]);
                }
            },
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:90px;text-align: center;'],
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($model) {
                $sqlDate = \Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d H:i:s');
                return \appxq\sdii\utils\SDdate::mysql2phpThDateSmall($sqlDate) . '<br><code>' . \appxq\sdii\utils\SDdate::getPrettyTime($sqlDate) . '</code>';
            },
            'format' => 'raw',
//            'filter' => DatePicker::widget([
//                //'model' => $searchModel,
//                'attribute' => 'created_at',
//                'dateFormat' => 'php:Y-m-d',
//                'options' => [
//                    'class' => 'form-control'
//                ]
//            ]),
            'contentOptions' => ['style' => 'width:220px;'],
        ],
        [
            'attribute' => 'last_login_at',
            'format' => 'raw',
            'contentOptions' => ['style' => 'width:220px;'],
            'value' => function ($model) {
                if(!empty($model->last_login_at)){
                    $sqlDate = \Yii::$app->formatter->asDate($model->last_login_at, 'php:Y-m-d H:i:s');
                    return \appxq\sdii\utils\SDdate::mysql2phpThDateSmall($sqlDate) . '<br><code>' . \appxq\sdii\utils\SDdate::getPrettyTime($sqlDate) . '</code>';
                }else{
                    return '-';
                }
            },
        ],
        [
            'header' => 'Active',
            'value' => function ($model) {

                if ($model->isConfirmed) {
                    return '<i style="color:green;" class="glyphicon glyphicon-ok"></i>';
                } else {
                    return '<i style="color:red;" class="glyphicon " style="padding-right: 6px; padding-left: 6px;"></i>';
                }
            },
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:60px;text-align: center;'],
        ],
        [
            'header' => 'Status',
            'value' => function ($model) {
                $status = isset($model->profile->invite) ? $model->profile->invite : '';
                if($status == '3'){
                    return "<label class='label label-warning'>Waiting</label>";
                }else if($status == '2'){
                    return "<label class='label label-danger'>Rejected</label>";
                }
                else if($status == '4'){
                    return "<label class='label label-danger'>Discontinuatio</label>";
                }
                else if($status == '1'){
                    return "<label class='label label-success'>Accepted</label>";
                }else if($status == '' || empty($status)){
                    return "<label class='label label-success'>Accepted</label>";
                }
            },
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:60px;text-align: center;'],
        ],            
                    
//        [
//            'header' => Yii::t('user', 'Block status'),
//            'value' => function ($model) {
//                if ($model->isBlocked) {
//                    return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
//                                'class' => 'btn btn-xs btn-success btn-block',
//                                'data-method' => 'post',
//                                'data-action' => 'unblock',
//                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?')
//                    ]);
//                } else {
//                    return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
//                                'class' => 'btn btn-xs btn-danger btn-block',
//                                'data-method' => 'post',
//                                'data-action' => 'block',
//                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')
//                    ]);
//                }
//            },
//            'format' => 'raw',
//        ],
//        [
//          'label'=> Yii::t('user', 'Allow'),
//          'format'=>'raw',
//          'value'=>function($model){
//              if ($model->id != Yii::$app->user->getId()) {
//                    if ($model->isBlocked) {
//                        return Html::a(Yii::t('user', 'Off'), ['block', 'id' => $model->id], [
//                                    'class' => 'btn btn-xs btn-danger',
//                                    'data-method' => 'post',
//                                    'data-action' => 'unblock',
//                                    'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?')
//                        ]);
//                    } else {
//                        return Html::a(Yii::t('user', 'On'), ['block', 'id' => $model->id], [
//                                    'class' => 'btn btn-xs btn-success btn-block',
//                                    'data-method' => 'post',
//                                    'data-action' => 'block',
//                                    'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')
//                        ]);
//                    }
//                }
//            }
//        ],            
        ['class' => 'yii\grid\ActionColumn',
            
            'header' => Yii::t('user', 'Manage'),
            'template' => '{update} {block} {delete} {verified}',
            'headerOptions' => ['style' => 'width:300px'],
            'buttons' => [
                'update' => function ($url, $model) {
                    if (Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite')) {
                        return Html::a('<span class="fa fa-edit"></span> '.Yii::t('chanpan', 'Edit'), yii\helpers\Url::to(['/user/admin/update-profile', 'id' => $model->id]), [
                                'title' => Yii::t('chanpan', 'Edit'),
                                'class' => 'btn btn-warning btn-xs',
                                'data-action' => 'update',
                                'id'=>"update{$model['id']}"
                                    
                        ]);
                    }
                },
                'block' => function ($url, $model) {
                     if ($model->id != Yii::$app->user->getId()) {
                        if ($model->isBlocked) {
                               return Html::a(Yii::t('user', 'Allow [Off]'), ['block', 'id' => $model->id], [
                                           'class' => 'btn btn-xs btn-danger',
                                           'data-method' => 'post',
                                           'data-action' => 'unblock',
                                           'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?')
                               ]);
                           } else {
                               return Html::a(Yii::t('user', 'Allow [On]'), ['block', 'id' => $model->id], [
                                           'class' => 'btn btn-xs btn-success',
                                           'data-method' => 'post',
                                           'data-action' => 'block',
                                           'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')
                               ]);
                           }
                     }
                },
                'delete' => function ($url, $model) {
                    if (Yii::$app->user->can('administrator')) {
                        if ($model->id != Yii::$app->user->getId()) {
                            return Html::a('<span class="fa fa-trash"></span> '.Yii::t('chanpan', 'Delete'), $url, [
                                        'title' => Yii::t('chanpan', 'Delete'),
                                        'class' => 'btn btn-danger btn-xs',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-action' => 'delete',
                                        'id'=>"delete{$model['id']}"
                            ]);
                        }
                    }
                },
                'verified' => function ($url, $model) {
                    if (Yii::$app->user->can('administrator') && $model->id != Yii::$app->user->getId() && $model->confirmed_at == '') {
                        return Html::a('<span class="fa fa-edit"></span> '.Yii::t('chanpan', 'Verified'), yii\helpers\Url::to(['/user/admin/verified']), [
                                    'title' => Yii::t('chanpan', 'Verified'),
                                    'class' => 'btn btn-success btn-xs btnVerified',
                                    'data-action' => 'verified',
                                    'data-id'=>$model->id,
                                    'id'=>"btn{$model->id}"
                                            
                        ]);
                    }
                },        
            ],
            'contentOptions' => ['style' => 'width:160px;text-align:left;']
        ],
    ],
]);
?>
<div>
    <b style="color: #eb622c;margin-top:5px;">"Admin Service" is the default member who own every new created Project. Therefor, it is recommended that the Project Creator or Owner should click Edit->Account tab to change the Password.
     &nbsp;"All other information should represent someone who will be the point of contact for the Project. Therefore, it is best to enter the Office's or Project's e-mail and phone number."</b>
</div>
<div>
 
</div>
<?php $this->registerJs("
$('.btnVerified').on('click', function(){
    let url = $(this).attr('href');
    let id = $(this).attr('data-id'); 
    $.post(url, {id:id}, function(result){
        setTimeout(function(){
            $('#btn'+id).remove();
        },500);
        " . SDNoty::show('result.message', 'result.status') . "
    });
    return false;
    
});     
$('#gridUser thead tr th input[type=\'checkbox\'] , #gridUser tbody tr td input[type=\'checkbox\']').change(function(){
    let keys = $('#w0').yiiGridView('getSelectedRows');
    //console.log(keys);
    if(keys.length > 0){
        $('#btnDeleteAll').attr('disabled', false);
    }else{
        $('#btnDeleteAll').attr('disabled', true);
    }
}); 
$('#view-user .grid-view, #gridUser tbody tr td input[type=\'checkbox\']').on('change',function(){
    let keys = $('#view-user #w0').yiiGridView('getSelectedRows');
    if(keys.length > 0){
        $('#btnDeleteAll').attr('disabled', false);
    }else{
        $('#btnDeleteAll').attr('disabled', true);
    }
});
//$('#btnDeleteAll').on('click', function(){
//    let keys = $('#w0').yiiGridView('getSelectedRows');
//    DeleteAll(keys);
//});
$('#view-user #btnDeleteAll , #btnDeleteAll').on('click', function(){
    let keys = $('#w0').yiiGridView('getSelectedRows');
    if(keys.length > 0){
    }else{
        keys = $('#view-user #w0').yiiGridView('getSelectedRows');
    }
     
    DeleteAll(keys);
});

function DeleteAll(keys){
//    console.log(keys);
    let data = [];
    keys.map(function(k, i){
        data[i]={index:i, id:k};
    });
    if(keys.length > 0){
       yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
            let url = '".yii\helpers\Url::to(['/user/admin/delete-all'])."';
            $.post(url,{data:data}, function(result){
                if(result.status == 'success'){
                   initUser();
                        " . SDNoty::show('result.message', 'result.status') . "
		   } else {
			" . SDNoty::show('result.message', 'result.status') . "
		   }
            });
       });
    }
}


$('ul.pagination li a').click(function(){
    let url = $(this).attr('href');
    $.get(url, function(data){
        $('#view-user').html(data);
    });
    return false;
});
$('.table thead tr th a').click(function(){
    let url = $(this).attr('href');
    $.get(url, function(data){
        $('#view-user').html(data);
    });
    return false;
});

$('.manager-btn').click(function(){
    if($(this).hasClass('btn-default')){
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
        $(this).html('<i class=\'glyphicon glyphicon-ok\'></i>');
    }else{
        $(this).removeClass('btn-primary');
        $(this).addClass('btn-default');
        $(this).html('<i class=\"glyphicon \" style=\"padding-right: 6px; padding-left: 6px;\"></i>');
       
    }
});
 
$('.btn').click(function(){
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    if(action == 'update'){
        modalUser(url);
        return false;
    }else if(action === 'delete') {
		yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
                                        initUser();
					" . SDNoty::show('result.message', 'result.status') . "
				} else {
					" . SDNoty::show('result.message', 'result.status') . "
				}
			}).fail(function(){
				" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
				console.log('server error');
			});
		})
    }else if(action == 'block'){
        yii.confirm('" . Yii::t('user', 'Are you sure you want to Allow [Off] this user?') . "', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					" . SDNoty::show('result.message', 'result.status') . "
					 
                                        
				} else {
					" . SDNoty::show('result.message', 'result.status') . "
                                         
				}
                               initUser();
			}).fail(function(){
				" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
				console.log('server error');
			});
		})
    }
    else if(action == 'unblock'){
        yii.confirm('" . Yii::t('user', 'Are you sure you want to Allow [On] this user?') . "', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					" . SDNoty::show('result.message', 'result.status') . "
					//$.pjax.reload({container:'#user-grid-pjax'});
				} else {
					" . SDNoty::show('result.message', 'result.status') . "
                                            //$.pjax.reload({container:'#user-grid-pjax'});
				}
                                initUser();
			}).fail(function(){
				" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
				console.log('server error');
			});
		})
    }
    else if(action == 'admin'){
            let uri = $(this).attr('data-url');
			$.post(
				uri
			).done(function(result){
				if(result.status == 'success'){
					" . SDNoty::show('result.message', 'result.status') . "
					//$.pjax.reload({container:'#user-grid-pjax'});
				} else {
					" . SDNoty::show('result.message', 'result.status') . "
                                            //$.pjax.reload({container:'#user-grid-pjax'});
				}
                                initUser();
			}).fail(function(){
				" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
				console.log('server error');
			});
		 
    }
    return false;
});

function modalUser(url) {
    $('#modal-user .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-user').modal('show')
    .find('.modal-content')
    .load(url);
}

$('#user-grid-pjax').on('click', '.manager-btn', function(){
    updateAttr($(this).attr('data-url'));
});
$(\"[data-toggle='tooltip']\").tooltip();
function updateAttr(url) {
    $.post(
	url
    ).done(function(result){
        console.log(result);
	if(result.status == 'success'){
	    " . appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') . "
	    $.pjax.reload({container:'#user-grid-pjax'});
	} else {
	    " . appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') . "
	}
    }).fail(function(){
	console.log('server error');
    });
}
"); ?>
 

<?php \richardfan\widget\JSRegister::begin();?> 
<script>
    // $('#txtSearch').on('change', function(){
    //     getSearch();
    //    return false;
    // });
    $('#btnSearch').click(function(e){       
       getSearch();
       return false;
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('.filter-date').hide();
    $('#btnSetting').on('click', function(){
        let filter = $('#filter').val();
        if(filter=='hide'){
            $('#filter').val('show');
        } else {
            $('#filter').val('hide');
        }
        $('.filter-date').toggle('slow');
    });

    function getSearch(){
        let serach_name = $('#txtSearch').val();
        let fromdate = '';
        let todate = '';
        if($('#filter').val() =='show'){
            fromdate = $('#fromdate').val();
            todate = $('#todate').val();
        }
        let url = '/user/admin/index';
        $.get(url,{serach_name:serach_name,fromdate,todate}, function(data){
           $('#view-user').html(data);
        });
        return false; 
    }
</script>
<?php \richardfan\widget\JSRegister::end();?>