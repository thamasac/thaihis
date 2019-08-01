<?php

use backend\modules\ezforms2\classes\BtnBuilder;
use backend\modules\ezforms2\classes\EzfHelper;

//\appxq\sdii\utils\VarDumper::dump($reloadDiv);
?>
<div class="pull-right" style="margin-bottom: 10px;">
    <?=
            BtnBuilder::btn()
            ->ezf_id($name_ezf)
            ->target($type_id)
            ->reloadDiv($reloadDiv)
            ->label("<i class='fa fa-plus'></i> Add New Document")
            ->options(['class' => 'btn btn-success'])
            ->buildBtnAdd();
    ?>
</div>
<div class="clearfix"></div>
<?php
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];




$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

foreach ($column as $field) {

    $fieldName = $field;
    if (is_array($field) && isset($field['attribute'])) {
        $fieldName = $field['attribute'];
    }

    $changeField = TRUE;
    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];
        if ($fieldName == $var && $var != 'target') {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

//    $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            if ($var == $field_taget) {
                $colTmp = [
                    'attribute' => $var,
                    'label' => $label,
                    'value' => function ($data) use($dataInput, $value,$field_taget,$type_ezf) {
                        $query = new yii\db\Query();
                        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getFormTableName($type_ezf);
                        $dataRef = $query->select('F2v1')->from($ezform['ezf_table'])->where('id=:id',[':id'=>$data[$field_taget]])->one();
//                        \appxq\sdii\utils\VarDumper::dump($field_taget);
                        return $dataRef['F2v1'];
                    },
                    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
                    'contentOptions' => ['style' => "min-width:100px;"],
                    'filter' => $htmlFilter,
                ];
            } else {
                $colTmp = [
                    'attribute' => $var,
                    'label' => $label,
                    'value' => function ($data) use($dataInput, $value,$field_taget,$type_ezf) {
                    $text =backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $data);
                    if($text == ''){
                        
                    }else{
                        return $text;
                    }
                    },
                    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
                    'contentOptions' => ['style' => "min-width:100px;"],
                    'filter' => $htmlFilter,
                ];
            }

            if (is_array($field) && isset($field['attribute'])) {
                $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
            }

            $changeField = FALSE;
            $columns[] = $colTmp;
//    break;
        }
    }
}

$columns[] = [
    'label' => 'View',
    'format' => 'raw',
    'value' => function ($data) use($field_taget, $ezf_id, $id, $pagSize, $column, $detail_ezf) {
        return yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', yii\helpers\Url::to(['/ezforms2/tmf/get-sub-grid',
                            'ezf_id' => $ezf_id,
                            'field_taget' => $field_taget,
                            'target' => $data['target'],
                            'id' => $data[$field_taget],
                            'pageSize' => $pagSize,
                            'column' => $column,
                            'detail_ezf' => $detail_ezf,
                            'reloadDiv' => $reloadDiv
                        ]), [
                    'data-action' => 'update',
                    'title' => Yii::t('yii', 'View'),
                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                    'class' => 'btn btn-primary btn-xs btnViewDoc',
        ]);
    },
    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
    'contentOptions' => ['style' => "min-width:100px;"],
];



//\yii\widgets\Pjax::begin(['id' => 'pjax-grid-' . $reloadDiv]);
echo \appxq\sdii\widgets\GridView::widget([
    'id' => 'grid-' . $reloadDiv,
    'dataProvider' => $dataProvider,
    'columns' => $columns
]);
//\yii\widgets\Pjax::end();
richardfan\widget\JSRegister::begin(['position' => yii\web\View::POS_READY]);
?>
<script>
    $('#grid-<?= $reloadDiv ?> .pagination a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $reloadDiv ?> thead tr th a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $reloadDiv ?> tbody tr td .btnViewDoc').on('click', function () {
        var div = $(this);
        $('.modal-content-grid').modal();
        $.ajax({
            url: $(this).attr('href'),
            method: 'get',
            type: 'html',
            success: function (result) {
//                div.parents('tr').after("<tr>"+result+"</tr>");
                $('.modal-content-grid').find('.modal-body').html(result);
            }
        });
//        getGridView($(this).attr('href'),$(this));
        return false;
    });

    function getGridView(url, div) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#' + div).append(result);
            }
        });
    }

    function getSubGrid(url) {
        $('.modal-content-grid').modal();
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('.modal-content-grid').find('.modal-body').html(result);
            }
        });
    }
</script>
<?php
richardfan\widget\JSRegister::end();
