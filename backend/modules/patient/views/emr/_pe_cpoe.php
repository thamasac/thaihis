
<dl class="dl-horizontal">
  <dt style="width: 35px;">PE:</dt>
  <dd style="margin-left: 40px;">
    <?php
    if (isset($model['id'])) {
        if ($model['pe_n_all'] == '1') {
            $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'pe_n_all', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            echo ' <strong>' . $modelFields['ezf_field_label'] . '</strong> : ' . \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
        } elseif ($model['pe_n_all'] == '2') {
            $pe = ['pe_head', 'pe_neck', 'pe_breast', 'pe_heart', 'pe_lung', 'pe_abdomen'];
            foreach ($pe as $value) {
                if ($model[$value] == '2') {
                    $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $value, ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    echo '<div> <strong>' . $modelFields['ezf_field_label'] . '</strong> : ' . \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model) . '</div>';
                }
            }
        }
        $pe = ['pe_ga_1', 'pe_ga_2', 'pe_ga_3', 'pe_ga_5', 'pe_ga_6', 'pe_ga_7', 'pe_ga_8'];
        $i = 0;
        foreach ($pe as $value) {
            echo ($i == 0 ? '<div><strong>General Appearance : </strong>' : '');
            if ($model[$value]) {
                $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $value, ':ezf_id' => $ezf_id])->one();

                echo $modelFields['ezf_field_label'] . ' ,';
            }
            $i++;
        }
    }
    ?>
  </dd>
</dl>