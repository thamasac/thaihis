<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Clinicaldata */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'System Error');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">

    <div class='col-md-4 col-sm-4 col-xs-4'>
        <div class="panel panel-success">
            <div class="panel-heading"><?= Yii::t('app', 'All url projects') ?></div>
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-md-10 col-xs-8">
                    <?php 
                        echo \kartik\select2\Select2::widget([ 
                            'data'=>[],
                            'name' => 'term',
                            'options' => ['placeholder' => 'Select url ...'],
                            'language' => \Yii::$app->language,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0, 
                                'ajax' => [
                                    'url' => Url::to(['/log/default/get-url']),
                                    'dataType' => 'json', //รูปแบบการอ่านคือ json
                                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term};}')
                                ],
                                'escapeMarkup' => new \yii\web\JsExpression('function(markup) { return markup;}'),
                                'templateResult' => new \yii\web\JsExpression('function(prefix){ return prefix.text;}'),
                                'templateSelection' => new \yii\web\JsExpression('function(prefix) {return prefix.text;}'),
                            ],
                            'pluginEvents' => [
                                "select2:select" => "function(e) {  
                                   searchUrl(e); 
                                }",
                             ]
                            
                        ]);
 
                    ?>
                    

                </div>
                    <div class="col-md-2 col-xs-4">
                        <a href='<?= Url::to(['/log'])?>' class="btn btn-info"><?= Yii::t('app','All')?></a>
                    </div>
                </div>
                
                <br>
                <?=
                \appxq\sdii\widgets\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{summary}\n{pager}\n{items}\n{pager}",
                    'columns' => [
                        [
                            'format' => 'raw',
                            'attribute' => 'url',
                            'label'=>'',
                            'value' => function($model) {
                                $term = \Yii::$app->request->get('term', '');
                                $id = Yii::$app->request->get('id');
                                $page = Yii::$app->request->get('page');
                                $per_page = Yii::$app->request->get('per-page');
                                
                                $active = 'data-active';
                                if($id != $model->id){
                                    $active = '';
                                }
                                
                                $html = "";

                                $html .= "<a page='{$page}' per-page='{$per_page}' href='" . Url::to(['/log']) . "' data-id='{$model->id}' term='{$term}' class='linkUrl {$active}' style='display: block;
    width: 100%;
    position: relative;
    text-decoration: none;'>";
                                $html .= " {$model->url} ";
                                $html .= "<span class='badge' id='badge-{$model['id']}'  style='position: absolute;
    right: 10px;'><i class='fa fa-circle-o-notch fa-spin fa-fw'></i></span>";
                                $html .= $this->registerJs("
                                   
                                                    var url = '" . Url::to(['/log/default/get-count']) . "';
                                                    var id = '{$model['id']}';
                                                    $.get(url,{id:id}, function(res){
                                                        $('#badge-{$model['id']}').text(res);
                                                    });    
                                                
                                ");

                                $html .= "</a>";



                                return $html;
                            }
                        ]
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
    <div class='col-md-8 col-sm-8 col-xs-8'>
        <div class="panel panel-warning">
            <div class="panel-heading"><?= Yii::t('app', 'System Error') ?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9 col-xs-9 col-sm-9">
                        <?php yii\bootstrap\ActiveForm::begin(['action' => '/log', 'method' => 'get', 'id' => 'frmSystemError']); ?>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="<?= Yii::t('app', 'Search for message or date') ?>" name="term_err" id="term_err">
                            <div class="input-group-btn">
                                <button class="btn btn-primary" type="submit">
                                    <i class="glyphicon glyphicon-search"></i>
                                </button>
                            </div>
                        </div>
                        <?php yii\bootstrap\ActiveForm::end(); ?>
                    </div>
                    <div class="col-md-3 col-xs-3 col-sm-3">
                        <?php
                        $id = Yii::$app->request->get('id');
                        ?>
                        <?php if ($id != ''): ?>
                            <button class="btn btn-danger btnClear" data-url='<?= yii\helpers\Url::to(['/log/default/clear']) ?>' data-id='<?= $id ?>'><?= Yii::t('app', 'Clear All') ?></button>
                        <?php endif; ?>
                    </div>
                </div>
                <br>
                <div class="table table-responsive">
                    <?php Pjax::begin(['id' => 'error-grid-pjax']); ?>
                    <?=
                    yii\grid\GridView::widget([
                        'dataProvider' => $errorProvider,
                        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
                        'rowOptions' => function($model) {
                            return ['id' => "row-{$model['id']}"];
                        },
                        'columns' => [
                            [
                                'class' => 'appxq\sdii\widgets\ActionColumn',
                                'contentOptions' => ['style' => 'width:80px;text-align: center;'],
                                'template' => '{view} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<span class="fa fa-eye"></span> ', yii\helpers\Url::to(['/log/default/view?id=' . $model['id']]), [
                                                    'title' => Yii::t('app', 'View'),
                                                    'class' => 'btn btn-default btn-xs btnView',
                                                    // 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                                    'data-method' => 'get',
                                                    'data-action' => 'view',
                                                    'data-pjax' => 0,
                                                    'data-id' => Yii::$app->request->get('id'),
                                                    'data-error-id' => $model['id'],
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a('<span class="fa fa-trash"></span> ', yii\helpers\Url::to(['/log/default/delete?id=' . $model['id']]), [
                                                    'title' => Yii::t('app', 'Delete'),
                                                    'class' => 'btn btn-danger btn-xs btnDelete',
                                                    // 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                                    'data-method' => 'post',
                                                    'data-action' => 'delete',
                                                    'data-pjax' => 0,
                                                    'data-id' => Yii::$app->request->get('id'),
                                                    'data-error-id' => $model['id'],
                                        ]);
                                    },
                                ]
                            ],
                            [
//                                'contentOptions' => ['style' => 'width:200px;'],
                                'format' => 'raw',
                                'attribute' => 'message',
                                'value' => function($model) {
                                    return isset($model['message'])?$model['message']:'';
                                }
                            ],
                            [
//                                'contentOptions' => ['style' => 'width:180px;'],
                                'format' => 'raw',
                                'attribute' => 'created_at',
                                'value' => function($model) {
                                    return $model['created_at'];
                                }
                            ],
                        ],
                    ])
                    ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
  <?= ModalForm::widget([
        'id' => 'modal-log',
        'size'=>'modal-lg',
    ]);
    ?>

<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    function searchUrl(evt){
        let term = evt.params.data.id; 
        let url = '<?= Url::to(['/log/default/index?term=']); ?>'+term+'&id='+term;
        location.href = url;
        return false;
    };
    $(".linkUrl").on('click', function () {
        let url = $(this).attr('href');
        let id = $(this).attr('data-id');
        let term = $(this).attr('term');
        let page = $(this).attr('page');
        let per_page = $(this).attr('per-page');
        
        
        url = `${url}?term=${term}&id=${id}&page=${page}&per-page=${per_page}`;
        location.href = url;
        return false;
    });
    $("#frmSystemError").on('submit', function () {

        let term_err = $('#term_err').val();
        let term = '<?= Yii::$app->request->get('term') ?>';
        let id = '<?= Yii::$app->request->get('id') ?>';
        let url = $(this).attr('action');
        url = `${url}?term=${term}&id=${id}&term_err=${term_err}`;
        location.href = url;
        return false;
    });
    //btnView
    $(".btnView").on('click', function () {
        let url = $(this).attr('href');
        let id = $(this).attr('data-id');
        let errorid = $(this).attr('data-error-id');
        url = `${url}&id=${id}&errorid=${errorid}`;
        //alert(url);
        modalLog(url);
        return false;
    });
    function modalLog(url) {
        $('#modal-log .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-log').modal('show')
                .find('.modal-content')
                .load(url);
    }
    
    $(".btnDelete").on('click', function () {
        let url = $(this).attr('href');
        let id = $(this).attr('data-id');
        let errorid = $(this).attr('data-error-id');

        yii.confirm('<?= Yii::t('chanpan', 'Are you sure you want to delete this item?') ?>', function () {
            $.post(url, {id: id, errorid: errorid}).done(function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    $('#row-' + errorid).fadeOut('slow');
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
                console.log('server error');
            });
        });
        return false;
    });

    $(".btnClear").on('click', function () {
        let url = $(this).attr('data-url');
        let id = $(this).attr('data-id');

        yii.confirm('<?= Yii::t('chanpan', 'You want to clear all the data?') ?>', function () {
            $.post(url, {id: id}).done(function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
                console.log('server error');
            });
        });
        return false;
    });


</script>
<?php richardfan\widget\JSRegister::end(); ?>

<?php                    \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .data-active {
        background: #f9f9f9;
        color: #337ab7;
        font-weight: bold;
        font-size: 16px;
    }
</style>
<?php                    \appxq\sdii\widgets\CSSRegister::end();?>