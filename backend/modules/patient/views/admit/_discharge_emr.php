<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientHelper;

?>
<div class="row">
    <div class="col-md-12">
        <table class="table" style="margin-bottom: 0px"> 
            <tbody>
                <tr>                    
                    <td class="col-md-2 text-right"><?= Yii::t('patient', 'Admit Date') ?> : </td>
                    <td class="col-md-3"><label class="text-info"><?php
                            if ($model['admit_date']) {
                                echo SDdate::mysql2phpThDateTime($model['admit_date']);
                            }
                            ?></label></td>

                    <td class="col-md-2 text-right"><?= Yii::t('patient', 'Discharge Date') ?> : </td>
                    <td class="col-md-3"><label class="text-info"><?php
                            if ($model['discharge_date']) {
                                echo SDdate::mysql2phpThDateTime($model['discharge_date']);
                            }
                            ?></label></td>
                    <td class="col-md-2">LOS : <label class="text-info"><?= $model['LOS']; ?></label> Day</td>
                </tr>  
                <tr>
                    <td colspan="5"> Principal Diagnosis : <?= $model['di_txt']; ?></td>
                </tr>   
                <tr>
                    <td colspan="5"> Comorbidity : <label class="text-info"><?= $dataDiagComo ?></label>
                        
                    </td>
                </tr>  
                <tr>
                    <td colspan="5"> Complication : <label class="text-info"><?= $dataDiagComp ?></label>
                        
                    </td>
                </tr>  
                <tr>
                    <td colspan="5"> Operation : <label class="text-info"><?= $dataOperat ?></label>
                        
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <tbody>
                <tr>
                    <td class="col-md-6">Discharge Code : <label class="text-info"> <?php
                            if ($model['discharge_code']) {
                                $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'discharge_code', ':ezf_id' => $ezf_id])->one();
                                if (isset(Yii::$app->session['ezf_input'])) {
                                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                                }
                                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                            }
                            ?></label></td>
                    <td class="col-md-6">Discharge Status : <label class="text-info"><?php
                            if ($model['discharge_status']) {
                                $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'discharge_status', ':ezf_id' => $ezf_id])->one();
                                if (isset(Yii::$app->session['ezf_input'])) {
                                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                                }
                                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                            }
                            ?></label></td>
                </tr>
                <tr>
                    <td colspan="5"> Home Medication : <label class="text-info"></label></td>
                </tr>
                <tr>
                    <td colspan="5"> Date Follow up : <label class="text-info"></label></td>
                </tr>                
            </tbody>
        </table>
    </div>
</div>