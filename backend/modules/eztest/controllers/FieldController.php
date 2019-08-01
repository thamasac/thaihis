<?php

namespace backend\modules\eztest\controllers;

use yii\web\Controller;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\Ezform;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use backend\modules\ezforms2\models\EzformInput;
use appxq\sdii\helpers\SDHtml;

/**
 * Default controller for the `eztest` module
 */
class FieldController extends Controller {

    //ตัวอย่าง data ของ dropdownlist
    /*
    $data = [
        'builder' => [
            1501037872076708100 => [
                'value' => '1'
                'label' => 'ตัวเลือกที่ 1'
                'action' => 'update'
            ]
            1501574959057012700 => [
                'value' => '4'
                'label' => 'ตัวเลือกที่ 4'
                'action' => 'create'
            ]
            1501574991072642500 => [
                'value' => '4'
                'label' => 'ตัวเลือกที่ 4'
                'other' => [
                    'attribute' => 'var_19_other_4'
                    'id' => '1501574992058918700'
                    'action' => 'create'
                    'suffix' => ''
                ]
                'action' => 'create'
            ]
        ]
        'delete' => [
            '1501037874054687300', '1501037874054687301'
        ]
        'delete_fields' => [
            1501037875021872900 => 'var_19_other_3'
        ]
    ]
     * 
     * ตัวอย่างของ scale
    $data = [
        'builder' => [
            1501575515011625700 => [
                'fields' => [
                    '1_1' => [
                        'data' => [
                            1501575515011627100 => [
                                'value' => '5'
                                'label' => 'ดีมาก'
                                'action' => 'create'
                            ]
                            1501575515011627800 => [
                                'value' => '4'
                                'label' => 'ดี'
                                'action' => 'create'
                            ]
                            1501575515011628400 => [
                                'value' => '3'
                                'label' => 'พอใช้'
                                'action' => 'create'
                            ]
                            1501575515011629000 => [
                                'value' => '2'
                                'label' => 'แย่'
                                'action' => 'create'
                            ]
                            1501575515011629600 => [
                                'value' => '1'
                                'label' => 'แย่มาก'
                                'action' => 'create'
                            ]
                        ]
                        'label' => 'คำถามที่ 1'
                        'attribute' => 'var_127_1'
                        'id' => '1501575515011625700'
                        'action' => 'create'
                    ]
                ]
            ]
            1501575517097955600 => [
                'fields' => [
                    '2_1' => [
                        'label' => 'คำถามที่ 2'
                        'attribute' => 'var_127_2'
                        'id' => '1501575517097955600'
                        'action' => 'create'
                    ]
                ]
            ]
            1501575523042249500 => [
                'fields' => [
                    '3_1' => [
                        'label' => 'คำถามที่ 3'
                        'attribute' => 'var_127_3'
                        'id' => '1501575523042249500'
                        'action' => 'create'
                    ]
                ]
            ]
        ]
        'delete_fields' => [
            1501038168094147300 => 'var_25_5'
            1501038167038038500 => 'var_25_4'
            1501038166064162000 => 'var_25_3'
            1501038165089025700 => 'var_25_2'
        ]
        'delete' => [
            0 => '1501038164006883300'
            1 => '1501038164006882700'
            2 => '1501038164006882100'
            3 => '1501038164006881400'
        ]
    ];
     * 
     * ตัวอย่างของ grid
     * 
    $data = [
        'builder' => [
            1501038181097852600 => [
                'fields' => [
                    '1_1' => [
                        'header' => [
                            1501038181097855500 => [
                                'label' => 'หัวข้อที่ 1'
                                'type' => 'checkbox'
                                'col' => '1'
                            ]
                            1501575808087101200 => [
                                'label' => 'หัวข้อที่ 5'
                                'type' => 'textinput'
                                'col' => '5'
                            ]
                            1501575809057287700 => [
                                'label' => 'หัวข้อที่ 6'
                                'type' => 'textinput'
                                'col' => '6'
                            ]
                        ]
                        'attribute' => 'var_26_1_1'
                        'label' => 'คำถามที่ 1'
                        'id' => '1501038181097852600'
                        'action' => 'update'
                    ]
                    '1_5' => [
                        'attribute' => 'var_26_1_5'
                        'label' => 'คำถามที่ 5'
                        'id' => '1501575808099102900'
                        'action' => 'create'
                    ]
                    '1_6' => [
                        'attribute' => 'var_26_1_6'
                        'label' => 'คำถามที่ 6'
                        'id' => '1501575809068810300'
                        'action' => 'create'
                    ]
                ]
            ]
            1501575806041218400 => [
                'fields' => [
                    '6_1' => [
                        'attribute' => 'var_26_6_1'
                        'label' => 'คำถามที่ 1'
                        'id' => '1501575806041379200'
                        'action' => 'create'
                    ]
                    '6_5' => [
                        'attribute' => 'var_26_6_5'
                        'label' => 'คำถามที่ 5'
                        'id' => '1501575809004956100'
                        'action' => 'create'
                    ]
                    '6_6' => [
                        'attribute' => 'var_26_6_6'
                        'label' => 'คำถามที่ 6'
                        'id' => '1501575809074290800'
                        'action' => 'create'
                    ]
                ]
            ]
            1501575807009094600 => [
                'fields' => [
                    '7_1' => [
                        'attribute' => 'var_26_7_1'
                        'label' => 'คำถามที่ 1'
                        'id' => '1501575807009264000'
                        'action' => 'create'
                    ]
                    '7_5' => [
                        'attribute' => 'var_26_7_5'
                        'label' => 'คำถามที่ 5'
                        'id' => '1501575809010654700'
                        'action' => 'create'
                    ]
                    '7_6' => [
                        'attribute' => 'var_26_7_6'
                        'label' => 'คำถามที่ 6'
                        'id' => '1501575809080044500'
                        'action' => 'create'
                    ]
                ]
            ]
        ]
        'delete_fields' => [
            1501038188029630600 => 'var_26_1_4'
            1501038188035616400 => 'var_26_2_4'
            1501038188042133600 => 'var_26_3_4'
            1501038190063243300 => 'var_26_4_4'
            1501038195013633500 => 'var_26_5_4'
            1501038181097854900 => 'var_26_1_3'
        ]
    ];
     * 
     * Map และทั่วไปที่ทำ 1 คำถาม หลาย input
     * 
    $data = [
    'builder' => [
        1501037903024161300 => [
            'fields' => [
                '1_1' => [
                    'attribute' => 'var_21_lat'
                    'id' => '1501037903024162900'
                    'label' => 'lat'
                    'action' => 'update'
                ]
                '1_2' => [
                    'attribute' => 'var_21_lng'
                    'id' => '1501037903024163600'
                    'label' => 'lng'
                    'action' => 'update'
                ]
            ]
        ]
    ]
    ];
    */
    public function actionCreateField($ezf_id) {

        $model = new EzformFields();
        $model->ezf_id = $ezf_id;
        $model->ezf_field_id = SDUtility::getMillisecTime();
        $model->ezf_field_name = 'var_ddd14';
        $model->ezf_field_order = EzfQuery::getFieldsCountById($model->ezf_id);
        $model->ezf_field_type = 51;//textinput

        $dataEzf = Ezform::find()->where('ezf_id=:id', [':id' => $model->ezf_id])->one();
        $dataInput = EzformInput::find()->where('input_id=:id', [':id' => $model->ezf_field_type])->one();
        
        $data = [];//จากตัวอย่างด้านบน
        $options = $dataInput['input_option'];
        $validate = $dataInput['input_validate'];

        $result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate);

        return $this->render('index', [
                    'result' => $result,
        ]);
    }

    public function actionUpdateField($ezf_field_id) {

        $model = EzformFields::find()->where('ezf_field_id=:ezf_field_id', [':ezf_field_id'=>$ezf_field_id])->one();
        if($model){
            $Oldmodel = $model;

            $model->ezf_field_data = SDUtility::string2Array($model->ezf_field_data);
            $model->ezf_field_options = SDUtility::string2Array($model->ezf_field_options);
            $model->ezf_field_specific = SDUtility::string2Array($model->ezf_field_specific);
            $model->ezf_field_validate = SDUtility::string2Array($model->ezf_field_validate);

            $dataEzf = Ezform::find()->where('ezf_id=:id', [':id' => $model->ezf_id])->one();
            $dataInput = EzformInput::find()->where('input_id=:id', [':id' => $model->ezf_field_type])->one();

            $data = [];
            $options = $dataInput['input_option'];
            $validate = $dataInput['input_validate'];

            $result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate);
            
        } else {
            $result = $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . 'ไม่พบ EzformFields',
                ];
        }
        
        return $this->render('index', [
                        'result' => $result,
            ]);
    }
    
    public function actionDeleteField($ezf_field_id, $ezf_id) {
        
        $dataEzf = Ezform::find()->where('ezf_id=:id', [':id' => $ezf_id])->one();
       
        $result = EzBuilderFunc::deleteEzField($ezf_field_id, $dataEzf);

        return $this->render('index', [
                    'result' => $result,
        ]);
    }
    
}
