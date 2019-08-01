<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 6/28/2018
 * Time: 12:15 PM
 */

use yii\bootstrap\Html;
use yii\data\SqlDataProvider;
use yii\grid\GridView;


/**
 * @var \yii\data\SqlDataProvider $dataProvider
 */


$count = Yii::$app->db->createCommand("SELECT COUNT(*) FROM zdata_project_join_request WHERE (permission = 0 or permission IS NULL ) AND rstat < 3 ", [])->queryScalar();
$dataProviderSql = 'SELECT id,user_data , profile_data , firstname, lastname, from_site  FROM zdata_project_join_request  WHERE (permission = 0 or permission IS NULL ) AND rstat < 3 ';
$dataProvider = new SqlDataProvider([
    'sql' => $dataProviderSql,
    'totalCount' => $count,
    'sort' => [
        'defaultOrder' => ['zdata_project_join_request.id'=>SORT_DESC],
        'attributes' => [
            'zdata_project_join_request.id',
            'zdata_project_join_request.firstname',
        ],
    ],
    'pagination' => [
        'pageSize' => 20,
    ],
]);
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
                    'attribute' => 'from_site',
                    'label' => Yii::t('chanpan', 'From Sitecode'),
                    'value' => function ($model) {
                        if (!empty($model["from_site"])) {
                            $sitecode = isset($model["from_site"]) ? $model["from_site"] : '';
                            return \common\modules\user\classes\SiteCodeFunc::getSiteCodeValue($sitecode);
                        } else {
                            return ' ';
                        }
                    },
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:90px;text-align: center;'],
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('user', 'Manage'),
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if ( Yii::$app->user->can('administrator') ) {
                                return Html::a('<span class="fa fa-edit"></span> ' . Yii::t('chanpan', 'Edit'), yii\helpers\Url::to(['/user/admin/collaborator-approve-view', 'id' => $model["id"] ]), [
                                    'title' => Yii::t('chanpan', 'Edit'),
                                    'class' => 'btn btn-warning btn-xs',
                                    'data-action' => 'update'
                                ]);
                            }
                            return "";
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
