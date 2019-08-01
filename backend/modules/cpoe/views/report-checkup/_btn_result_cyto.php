<?php

if ($data) {
    $btn = \yii\helpers\Html::a('<span class="fa fa-wpforms"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-primary ezform-main-open', 'data-modal' => 'modal-ezform-main',
                'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-view', 'ezf_id' => $ezf_id,
                    'modal' => 'modal-ezform-main', 'dataid' => $data['id'],
    ])]);
    $append = "$('[item-id=\"1514079832051038300\"] h3').append(' $btn'); ";
    $append .= "$('[item-id=\"1514079832051038300\"],[item-id=\"1514041957058634300\"],[item-id=\"1514042130056561300\"],[item-id=\"1514075083023229400\"]').removeClass('hidden'); ";
    $append .= "$('[item-id=\"1514075888002584200\"],[item-id=\"1514076238012413800\"],[item-id=\"1514076689004058600\"],[item-id=\"1514074534083919900\"],[item-id=\"1514049671071933100\"]').removeClass('hidden'); ";
    $append .= "$('[item-id=\"1516871849061955400\"],[item-id=\"1514079428020869600\"],[item-id=\"1518103712080793300\"],[item-id=\"1519313893059865000\"]').removeClass('hidden');";
    $append .= "$('[item-id=\"1514075298047426200\"],[item-id=\"1514079523022148600\"],[item-id=\"1514079683021640800\"],[item-id=\"1514075997097110100\"],[item-id=\"1519372725011734600\"]').removeClass('hidden');";
    //,[item-id=\"1514074534083919900\"],[item-id=\"1514049671071933100\"]
    $this->registerJS("$append ");
} else {
//    echo Yii::t('patient', 'No results');
}
?>