<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model,
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */

?>


<?php

use backend\modules\ezforms2\models\TbdataAll;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

$nameChart = $options['title'];
$pies = array();
var_dump($options);
$fields = $options["fields"];
$ezfArr = $options["ezf_id"];
$percentArr= $options["percent"];
//
foreach ($fields as $index => $field) {
    $fieldName = $field;
    $col = 12/$column;

    try {
        $ezform = \backend\modules\ezforms2\models\Ezform::find()->where(["ezf_id" => $options["ezf_id"][$index]])->one();
        $model = new TbdataAll();
        $model->setTableName($ezf_table);
        $model = $model->find()->where('id=:id AND rstat <> 3', [':id' => $ref_id])->one();
        if (!$model) {
            return FALSE;
        }
    } catch (\yii\db\Exception $e) {
        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        return FALSE;
    }

    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];
        if ($fieldName == $var) {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

//            $path_data = [
//                '{id}' => Html::getInputId($model, $fieldName),
//                '{label}' => $label,
//                '{value}' =>,
//            ];

            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
            // backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model)
            array_push($pies,[
                "name" => $label,
                "y" => 50,
            ]);
            break;
        }
    }
}
//var_dump($pies);

try {
    echo Highcharts::widget([
        'scripts' => [
            'modules/exporting',
            'themes/grid-light',
        ],
        'options' => [
            'title' => [
                'text' => "$nameChart",
            ],
            'labels' => [
                'items' => [
                    [
                        'html' => $options['label'],
                        'style' => [
                            'left' => '50px',
                            'top' => '18px',
                            'color' => new JsExpression('(Highcharts.theme && Highcharts.theme.textColor) || "black"'),
                        ],
                    ],
                ],
            ],
            'series' => [
                [
                    'type' => 'pie',
                    'name' => 'Total consumption',
                    'data' => [
                        [
                            'name' => 'Jane',
                            'y' => 13,
                            'color' => new JsExpression('Highcharts.getOptions().colors[0]'), // Jane's color
                        ],
                        [
                            'name' => 'John',
                            'y' => 23,
                            'color' => new JsExpression('Highcharts.getOptions().colors[1]'), // John's color
                        ],
                        [
                            'name' => 'Joe',
                            'y' => 19,
                            'color' => new JsExpression('Highcharts.getOptions().colors[2]'), // Joe's color
                        ],
                    ],
                    //                'center' => [200, 200],
                    //                'size' => 200,
                    'showInLegend' => false,
                    'dataLabels' => [
                        'format' => new JsExpression("'<b>{point.name}</b>: {point.y:,.0f}/{point.percentage:.1f} %'"),
                        'enabled' => true,
                    ],
                ],
            ],
        ]
    ]);
} catch (Exception $e) {
}

//end box content

?>

