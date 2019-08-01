<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezmodules\classes\ModuleQuery;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */
\backend\modules\ezmodules\assets\ModuleAsset::register($this);

$this->title = $model->ezm_name;
$userId = Yii::$app->user->id;
$userProfile = Yii::$app->user->identity->profile;

$icon = Html::img(ModuleFunc::getNoIconModule(), ['width' => 30, 'class' => 'img-rounded']);
if (isset($model->ezm_icon) && !empty($model->ezm_icon)) {
    $icon = Html::img($model['icon_base_url'] . '/' . $model['ezm_icon'], ['width' => 30, 'class' => 'img-rounded']);
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->ezm_name, 'url' => ['/ezmodules/ezmodule/view', 'id'=>$model->ezm_id]];
$this->params['breadcrumbs'][] = Yii::t('ezmodule', 'Report');
?>

<div id="ezmodule-main-app" class="ezmodule-view">

    <div class="modal-header">
        <?php
        $ezm_builder = explode(',', $model['ezm_builder']);
        if ((Yii::$app->user->can('administrator')) || $model['created_by'] == $userId || in_array($userId, $ezm_builder)) {
            echo Html::a('', ["/ezmodules/ezmodule/update", 'id' => $module], [
                'id' => 'modal-btn-ezmodule',
                'class' => 'fa fa-cog fa-2x pull-right underline',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('ezmodule', 'Module'),
            ]);
        }

        echo Html::a('', '', [
            'id' => 'modal-btn-info-app',
            'class' => 'fa fa-info-circle fa-2x pull-right underline info-app',
            'data-url' => yii\helpers\Url::to(['/ezmodules/default/info-app', 'id' => $module]),
            'data-toggle' => 'tooltip',
            'title' => Yii::t('ezmodule', 'Info'),
        ]);
        ?>
        <h4 class="modal-title"><?= $icon ?> <?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body" >
        <?php EzfStarterWidget::begin(); ?>
        <?=
        $this->render('_widget_menu', [
            'modelOrigin' => $model,
            'menu' => $menu,
            'module' => $module,
        ]);
        ?>
        <?php if (isset($model->ezf_id) && $model->ezf_id > 0) { ?>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'jump_menu',
                        'action' => ['report', 'id' => $module],
                        'method' => 'get',
            ]);
            ?>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="glyphicon glyphicon-stats"></i> <?=Yii::t('ezmodule', 'Report Config')?></h3>
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-3">
                            <h4><?=Yii::t('ezmodule', 'Select Forms')?></h4>
                        </div>
                        <div class="col-md-9">
                            <h4><?=Yii::t('ezmodule', 'Select Fields')?></h4>
                        </div>
                    </div>
                    <?php
                    $module_form = ModuleQuery::getFormsList($module, $userId);
                    $forms = ["$model->ezf_id"];

                    $targetField = EzfQuery::getTargetOne($model->ezf_id);
                    if (isset($targetField)) {
                        $forms[] = "{$targetField->ref_ezf_id}";
                        $specialField = EzfQuery::getSpecialOne($targetField->parent_ezf_id);
                    } else {
                        $specialField = EzfQuery::getSpecialOne($model->ezf_id);
                    }

                    $special = 0;
                    if (isset($specialField) && !empty($specialField)) {
                        $special = 1;
                    }

                    if (isset($module_form)) {
                        foreach ($module_form as $key => $value) {
                            $forms = ArrayHelper::merge($forms, ["{$value['ezf_id']}"]);
                            $options = SDUtility::string2Array($value['options']);

                            if (isset($options['forms'])) {
                                $forms = ModuleFunc::getFormOption($options, $forms);
                            }
                        }
                    }


                    $ezform = ModuleQuery::getFormsSelect(implode(',', $forms));
                    if (isset($ezform)) {

                        foreach ($ezform as $key => $value) {
                            $formItem = null;
                            $fieldItem = null;

                            if (isset($_GET['config'])) {
                                if (isset($_GET['config'][$value['ezf_id']]['form'])) {
                                    $formItem = $_GET['config'][$value['ezf_id']]['form'];
                                }
                                if (isset($_GET['config'][$value['ezf_id']]['field'])) {
                                    $fieldItem = $_GET['config'][$value['ezf_id']]['field'];
                                }
                            }
                            ?>

                            <div class="form-group">
                                <div class="row row_<?= $value['ezf_id'] ?>">
                                    <div class="col-md-3" style="padding-top: 8px;">
                                        <?= Html::checkbox("config[{$value['ezf_id']}][form]", $formItem, ['label' => $value['ezf_name']]) ?>
                                    </div>
                                    <div class="col-md-9">
                                        <?php
                                        $modelFields = ModuleQuery::getFieldsOptionList($value['ezf_id']);
                                        $dataFields = [];
                                        foreach ($modelFields as $keyF => $valueF) {
                                            $name = $valueF['name'];
                                            //??

                                            $dataFields[$valueF['id']] = $name;
                                        }
                                        ?>
                                        <?=
                                        Select2::widget([
                                            'name' => "config[{$value['ezf_id']}][field]",
                                            'value' => $fieldItem,
                                            'options' => ['placeholder' => 'Select ...', 'multiple' => true],
                                            'data' => $dataFields,
                                            'pluginOptions' => [
                                                'tags' => true,
                                            ],
                                        ])
                                        ?>
                                    </div>
                                </div>
                            </div>


                                <?php
                            }
                        }
                        ?>
                    <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-area-chart"></i> Submit', ['class' => 'btn btn-info']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

            <?php
            //	\yii\helpers\VarDumper::dump($ezform,10,true);
            //	exit();
            if (isset($_GET['config'])) {
                $config = $_GET['config'];
                $ezformMap = ArrayHelper::map($ezform, 'ezf_id', 'ezf_name');
                $ezformMapTable = ArrayHelper::map($ezform, 'ezf_id', 'ezf_table');

                $i = 1;
                foreach ($config as $ezf_id => $value) {
                    if (isset($value['form']) && $value['form'] == 1) {
                        ?>
                        <h4><i class="glyphicon glyphicon-list-alt"></i> <?= Yii::t('ezmodule', 'Form')?> <?= $ezformMap[$ezf_id] ?></h4>

                        <table class="table table-bordered table-hover"> 
                            <thead> 
                                <tr> 
                                    <th><?= Yii::t('ezmodule', 'Items')?></th> 
                                    <th style="width: 200px; text-align: right;"><?= Yii::t('ezmodule', 'Record')?></th> 
                                    <th style="width: 200px; text-align: right;"><?= Yii::t('ezmodule', 'Percent')?></th>
                                    <th style="width: 200px; text-align: right;"></th>
                                </tr> 
                            </thead> 
                            <?php
                            $select = '';
                            if (isset($value['field']) && !empty($value['field'])) {
                                $select = implode(',', $value['field']);
                            }
                            if ($select == '') {
                                continue;
                            }

                            $modelFormFields = \backend\modules\ezforms2\models\EzformFields::find()
                                    ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
                                    ->orderBy(['ezf_field_order' => SORT_ASC])
                                    ->all();

                            if (!isset(Yii::$app->session['ezf_input'])) {
                                Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
                            }
                            ?>
                            <tbody> 
                                <?php
                                if (isset($modelFormFields)) {
                                    foreach ($value['field'] as $varname) {
                                        foreach ($modelFormFields as $key => $field) {
                                            if ($field['ezf_field_name'] == $varname) {

                                                $dataInput;
                                                if (isset(Yii::$app->session['ezf_input'])) {
                                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], Yii::$app->session['ezf_input']);
                                                }
                                                ?>
                                                <?php
                                                $nameLabel = $field['ezf_field_label'];
                                                ?>
                                                <tr> 
                                                    <th><i class="glyphicon glyphicon-chevron-down"></i> <?= $nameLabel ?></th> 
                                                    <th style="width: 200px; text-align: right;"></th> 
                                                    <th style="width: 200px; text-align: right;"></th> 
                                                    <th style="width: 200px; text-align: right;"></th>
                                                </tr> 
                                                <?php
                                                $ezf_table = $ezformMapTable[$ezf_id];
                                                $fId = $field['ezf_field_id'];
                                                $fName = $field['ezf_field_name'];
                                                $fType = $field['ezf_field_type'];
                                                $fLabel = $field['ezf_field_label'];

                                                $sqlAddon = '';
                                                $params = [];
                                                if ($special == 1) {
                                                    $sqlAddon = ' AND hsitecode = :site';
                                                    $params[':site'] = $userProfile->sitecode;
                                                } else {
                                                    $sqlAddon = ' AND xsourcex = :site';
                                                    $params[':site'] = $userProfile->sitecode;
                                                }



                                                $sql = "SELECT *, count(*) AS row_num FROM {$ezformMapTable[$ezf_id]} WHERE rstat<>0 AND rstat<>3 $sqlAddon Group BY $fName ";
                                                $dataItems = Yii::$app->db->createCommand($sql, $params)->queryAll();
                                                $dataCount = count($dataItems);
                                                $select = ArrayHelper::map($dataItems, $fName, 'row_num');
                                                ArrayHelper::remove($select, '');

                                                $select = implode(',', array_keys($select));

                                                $categories = [];
                                                $dataChart = [];
                                                $dataPie = [];
                                                foreach ($dataItems as $keyItem => $item) {
                                                    $label = isset($item) ? backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $item) : 'Null';
                                                    $categories[] = $label;
                                                    echo $this->render('_itemReport', [
                                                        'label' => $label,
                                                        'num' => $item['row_num'],
                                                        'count' => $dataCount,
                                                        'model' => $model,
                                                        'field' => $item,
                                                        'rurl' => base64_encode(Yii::$app->request->url),
                                                        'ezf_id' => $ezf_id,
                                                    ]);

                                                    $dataChart[] = $item['row_num'] + 0;
                                                    $dataPie[] = ['name' => $categories[$keyItem], 'y' => $item['row_num'] + 0];
                                                }
                                                ?>
                                                <tr> 
                                                    <th style="text-align: right;"><?=Yii::t('ezmodule', 'Total')?></th> 
                                                    <th style="width: 200px; text-align: right;"><?= number_format($dataCount) ?></th> 
                                                    <th style="width: 200px; text-align: right;"></th> 
                                                    <th style="width: 200px; text-align: right;"></th> 
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <?=
                                                        miloschuman\highcharts\Highcharts::widget([
                                                            'options' => [
                                                                'title' => ['text' => $nameLabel],
                                                                'xAxis' => [
                                                                    'categories' => $categories
                                                                ],
                                                                'yAxis' => [
                                                                    'title' => ['text' => Yii::t('ezmodule', 'Record')]
                                                                ],
                                                                'series' => [
                                                                    ['name' => $nameLabel, 'data' => $dataChart]
                                                                ]
                                                            ]
                                                        ]);
                                                        ?>
                                                    </td>
                                                    <td colspan="3">
                                                        <?=
                                                        miloschuman\highcharts\Highcharts::widget([
                                                            'options' => [
                                                                'chart' => [
                                                                    'plotBackgroundColor' => NULL,
                                                                    'plotBorderWidth' => NULL,
                                                                    'plotShadow' => false,
                                                                    'type' => 'pie',
                                                                ],
                                                                'title' => ['text' => $nameLabel],
                                                                'tooltip' => ['pointFormat' => new JsExpression("'{series.name}: <b>{point.percentage:.1f}%</b>'")],
                                                                'plotOptions' => [
                                                                    'pie' => [
                                                                        'allowPointSelect' => true,
                                                                        'cursor' => 'pointer',
                                                                        'dataLabels' => [
                                                                            'enabled' => true,
                                                                            'format' => new JsExpression("'<b>{point.name}</b>: {point.percentage:.1f} %'"),
                                                                            'style' => [
                                                                                'color' => new JsExpression("(Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'")
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'series' => [
                                                                    [
                                                                        'name' => $nameLabel,
                                                                        'colorByPoint' => true,
                                                                        'data' => $dataPie
                                                                    ]
                                                                ]
                                                            ]
                                                        ]);
                                                        ?>


                                                    </td>
                                                </tr>
                                    <?php
                                    }
                                }
                            }
                        }
                        ?>
                            </tbody> 
                        </table>
                        <?php
                        $i++;
                    }
                }
            }
            ?>

<?php } //end report?>
<?php EzfStarterWidget::end(); ?>
    </div>

</div>
<?=
ModalForm::widget([
    'id' => 'modal-ezmodule',
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>

<?=
ModalForm::widget([
    'id' => 'modal-info-app',
        //'size'=>'modal-lg',
]);
?>

<?php $this->registerJs("
    
$('#modal-btn-ezmodule').on('click', function() {
    modalEzmodule($(this).attr('href'));
    return false;
});

$('#modal-ezmodule').on('hidden.bs.modal', function (e) {
  location.reload();
});

function modalEzmodule(url) {
    $('#modal-ezmodule .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule').modal('show')
    .find('.modal-content')
    .load(url);
}

$('#ezmodule-main-app').on('click', '.info-app', function() {
    modalApp($(this).attr('data-url'));
    return false;
});    

function modalApp(url) {
    $('#modal-info-app .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-info-app').modal('show')
    .find('.modal-content')
    .load(url);
}
    

"); ?>
