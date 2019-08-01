<?php

if ($data) {
    foreach ($data as $value) {
        echo \yii\helpers\Html::a('<span class="fa fa-child"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-primary ezform-main-open', 'data-modal' => 'modal-ezform-main',
            'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-view', 'ezf_id' => $ezf_id,
                'modal' => 'modal-ezform-main', 'dataid' => $value['id'],
        ])]);
    }
} else {
    echo Yii::t('patient', 'No results');
}
?>