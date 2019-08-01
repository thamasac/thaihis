<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 6/28/2018
 * Time: 12:15 PM
 */

use yii\bootstrap\Html;
use yii\grid\GridView;


/**
 * @var \yii\data\SqlDataProvider $dataProvider
 */


    if(!empty($_POST)){
        $serach_name = isset($_POST['serach_name']) ? $_POST['serach_name'] : '';
        $serach_role = isset($_POST['serach_role']) ? $_POST['serach_role'] : '';
        $dataSearch=['search_name'=>$serach_name, 'search_role'=>$serach_role];
        $dataProvider = \common\modules\user\classes\CNAdmin::queryRequestListUser($dataSearch, Yii::$app->user->identity->profile->sitecode);
    }else{
        $dataProvider = \common\modules\user\classes\CNAdmin::queryRequestListUser("", Yii::$app->user->identity->profile->sitecode);
    }

    try {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'tableOptions' => ['id' => 'gridUser', 'class' => 'table table-bordered table-responsive table-hover'],
            'layout' => "{items}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                ],
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => Yii::t('chanpan', 'Number'),
                ],
                [
                    'attribute' => 'firstname',
                    'label' => Yii::t('chanpan', 'First name'),
                ],
                [
                    'attribute' => 'lastname',
                    'label' => Yii::t('chanpan', 'Last name'),
                ],
                [
                    'attribute' => 'profile.sitecode',
                    'label' => Yii::t('chanpan', 'From Sitecode'),
                    'value' => function ($model) {
                        if (!empty($model["sitecode"])) {
                            $sitecode = isset($model["sitecode"]) ? $model["sitecode"] : '';
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
                    'attribute' => 'target_site',
                    'label' => Yii::t('chanpan', 'Target Sitecode'),
                    'value' => function ($model) {
                        if (!empty($model["target_site"])) {
                            $sitecode = isset($model["target_site"]) ? $model["target_site"] : '';
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
                    'attribute' => 'zdata_site_request.update_date',
                    'label' => Yii::t('chanpan', 'Request Date.'),
                    'value' => function ($model) {
                        $sqlDate = \Yii::$app->formatter->asDate($model["update_date"], 'php:Y-m-d H:i:s');
                        return \appxq\sdii\utils\SDdate::mysql2phpThDateSmall($sqlDate) . ' <code>' . \appxq\sdii\utils\SDdate::getPrettyTime($sqlDate) . '</code>';
                    },
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width:220px;'],
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('user', 'Manage'),
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if (Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite')) {
                                return Html::a('<span class="fa fa-edit"></span> ' . Yii::t('chanpan', 'Edit'), yii\helpers\Url::to(['/user/site-admin/update-profile', 'id' => $model["user_id"],'request_id' => $model["request_id"], 'target' => $model["target_site"]]), [
                                    'title' => Yii::t('chanpan', 'Edit'),
                                    'class' => 'btn btn-warning btn-xs',
                                    'data-action' => 'update'
                                ]);
                            }
                        },
                    ],
                    'contentOptions' => ['style' => 'width:160px;text-align:left;']
                ],
            ],
        ]);
    } catch (Exception $e) {
        echo "Grid view not working property. : ".$e->getMessage();
    }

$this->registerJs(<<<JS
$('.btn').click(function(){
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    if(action == 'update'){
        modalUser(url);
        return false;
    }
    });
    function modalUser(url) {
        $('#modal-user .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $('#modal-user').modal('show')
        .find('.modal-content')
        .load(url);
    }
JS
);
