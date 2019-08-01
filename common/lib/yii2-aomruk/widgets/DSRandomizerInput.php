<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\widgets;

use appxq\sdii\utils\VarDumper;
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use backend\modules\ezforms2\classes\EzformWidget;

/**
 * Description of DSRandomizer
 *
 * @author AR Soft
 */
class DSRandomizerInput extends InputWidget {

    public $ezf_id;
    public $ezf_field_id;
//    public $sitecode;
//    public $random_code;
//    public $check_sitecode;
    public $code = '';

    //put your code here
    public function init() {
        parent::init();

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $this->options['readonly'] = true;
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' form-control';
        } else {
            $this->options['class'] = 'form-control';
        }
//        if (isset($this->options['style'])) {
//            $this->options['style'] .= "re:none;" . $this->options['style'];
//        } else {
//            $this->options['style'] = "display:none;";
//        }
//        $this->options['readonly'] = true;

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id . "-randomizer";
        }
//        $this->options['readonly']= true;
        $this->options['data-ezf_id'] = $this->ezf_id;
        $this->options['data-ezf_field_id'] = $this->ezf_field_id;
        $profile = \Yii::$app->user->identity->profile;
        $sitecode = isset($this->options['sitecode']) && $this->options['sitecode'] != '' ? $this->options['sitecode'] : [];
        $ezf_id = isset($this->options['ezf_id']) && $this->options['ezf_id'] != '' ? $this->options['ezf_id'] : '';
        $random_code = isset($this->options['random_code']) && $this->options['random_code'] != '' ? $this->options['random_code'] : [];
        if (isset($this->options['check_sitecode']) && $this->options['check_sitecode'] == 1) {
            if (!empty($sitecode) && !empty($random_code)) {
                foreach ($sitecode as $key => $value) {
                    if ($value == $profile['sitecode']) {
                        $dataSitecode = \backend\modules\ezforms2\models\RandomCodeSite::find()->where(['sitecode' => $value, 'random_id' => $random_code[$key], 'ezf_id' => $ezf_id])->orderBy(['key' => SORT_DESC])->one();
                        $dataCode = \backend\modules\ezforms2\models\RandomCode::find()->where(['id' => $random_code[$key]])->one();
                        $code = [];
                        $start_row = 0;
                        if ($dataCode) {
                            $code = preg_split("/\r\n|\n|\r/", $dataCode['code_random']);
                            $start_row = isset($dataCode['start_row']) && $dataCode['start_row'] != '' ? $dataCode['start_row']-1 : 0;
                        }
//                        if (!$dataSitecode && $code != '' && $code[0] != '') {
                        if (!$dataSitecode && !empty($code) && isset($code[$start_row]) && $code[$start_row] != '') {
//                            $code = explode(',', $code[0]);
                            $code = explode(',', $code[$start_row]);
                            $this->code = $code[$dataCode['code_index'] - 1];
                        } else if ($dataSitecode && !empty($code) && isset($code[$dataSitecode['key'] + 1]) && $code[$dataSitecode['key'] + 1] != '') {
                            $code = explode(',', $code[$dataSitecode['key'] + 1]);
                            $this->code = $code[$dataCode['code_index'] - 1];
                        }

                    }
                }
            }
        } else if (isset($this->options['check_sitecode']) && $this->options['check_sitecode'] == 2) {
            if (!empty($sitecode) && !empty($random_code)) {
                foreach ($sitecode as $key => $value) {
                    if ($value == '') {
//                        VarDumper::dump($this->options);
                        $dataSitecode = \backend\modules\ezforms2\models\RandomCodeSite::find()->where(['sitecode' => $profile['sitecode'], 'random_id' => $random_code[$key], 'ezf_id' => $ezf_id])->orderBy(['key' => SORT_DESC])->one();
                        $dataCode = \backend\modules\ezforms2\models\RandomCode::find()->where(['id' => $random_code[$key]])->one();
                        $code = [];
                        $start_row = 0;
                        if ($dataCode) {
                            $code = preg_split("/\r\n|\n|\r/", $dataCode['code_random']);
                            $start_row = isset($dataCode['start_row']) && $dataCode['start_row'] != '' ? $dataCode['start_row']-1 : 0;
                        }
//                                    \appxq\sdii\utils\VarDumper::dump($code);
//                        if (!$dataSitecode && $code != '' && $code[0] != '') {
                        if (!$dataSitecode && !empty($code) && isset($code[$start_row]) && $code[$start_row] != '') {
//                            $code = explode(',', $code[0]);
                            $code = explode(',', $code[$start_row]);
                            $this->code = $code[$dataCode['code_index'] - 1];
                        } else if ($dataSitecode && !empty($code) && isset($code[$dataSitecode['key'] + 1]) && $code[$dataSitecode['key'] + 1] != '') {
                            $code = explode(',', $code[$dataSitecode['key'] + 1]);
                            $this->code = $code[$dataCode['code_index'] - 1];
                        }
                    }
                }
            }
        }

    }

    public function afterRun($result) {
        parent::afterRun($result);
        $this->registerClientScript();
    }

    public function run() {

        if ($this->hasModel()) {
            echo '<div class="input-group">' . EzformWidget::activeTextInput($this->model, $this->attribute, $this->options) . ' <span class="input-group-btn">' . Html::button('<span class="glyphicon glyphicon-random"></span> Randomizer', ['id' => $this->id, 'class' => 'btn btn-success ']) . '</span></div>';
        } else {
            echo '<div class="input-group">' . EzformWidget::textInput($this->name, $this->value, $this->options) . ' <span class="input-group-btn">' . Html::button('<span class="glyphicon glyphicon-random"></span> Randomizer', ['id' => $this->id, 'class' => 'btn btn-success ']) . '</span></div>';
        }
    }

    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
            setTimeout(()=>{
                if($('#{$this->options['id']}').attr('disabled') == 'disabled'){
                    $('#{$this->id}').attr('disabled',true);
                }
            },100);
                if($('#{$this->options['id']}').val() != ''){
                    $('#{$this->id}').attr('disabled',true);
                    $('#{$this->options['id']}').attr('readonly',true);
                }
                $('#{$this->id}').click(function(){
                        yii.confirm('" . Yii::t('app', 'Are you sure you want insert randomizetion code?') . "', function() {
//                                        var widget = $('#{$this->options['id']}');
//                                        var str = widget.attr('code_random');
//                                        var newData = [];
//                                        var check = false;
//                                        if(str != '' || typeof str != 'undefined'){
//                                            str = str.split('\\n');
//                                            for(var value of str){
//                                                var v = value;
//                                                if(value != '' && check == false){
//                                                    value = value.split(',');
//                                                    if(Array.isArray(value)){
//                                                        if(value.length < parseInt(widget.attr('max_index'))+1){
//                                                            newData.push(v+', data_id');
//                                                            widget.val(value[parseInt(widget.attr('code_index'))-1]);
//                                                            check = true;
//                                                        }
//                                                    }
//                                                }else{
//                                                    newData.push(v);
//                                                }
//                                            }
//                                        }
//                                        if(check == true){
//                                            var newCode = '';
//                                            for(var data of newData){
//                                                newCode += data+'\\n';
//                                            }
//                                            widget.attr('code_random',newCode.substr(0,newCode.length-2));
                                            var code = '{$this->code}';
                                            if(code != ''){
                                                $('#{$this->id}').attr('disabled',true);
                                                $('#{$this->options['id']}').val(code);
                                                $('#{$this->options['id']}').attr('readonly',true);
                                            }
//                                        }else{
//                                            
//                                        }
                        });
                    });
            ");
    }

}
