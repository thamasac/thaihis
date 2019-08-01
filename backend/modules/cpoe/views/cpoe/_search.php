<?php

use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;
?> 
<div class="form-group">      
    <?php
    $formatJs = <<< 'JS'

JS;

// Register the formatting script
    $labelName = Yii::t('patient', 'Name');
    $labelBdate = Yii::t('patient', 'Birthday - Age');
    $labelCitizen = Yii::t('patient', 'Citizen ID');
    $labelAddress = Yii::t('patient', 'Address');
    $this->registerJs(" var formatRepo = function (repo) {
    if (!repo.pt_hn) {
        return repo.text;
    }   
    var fulladdress = '';
    if(repo.pt_address){
        fulladdress = '$labelAddress : ' + 
        repo.pt_address +' ม.'+ repo.pt_moi +' ต.'+repo.DISTRICT_NAME +
        ' อ.'+repo.AMPHUR_NAME +' จ.'+repo.PROVINCE_NAME + ' ' + repo.pt_addr_zipcode;
    }
    var markup =
    '<div class=\"row\">' +   
        '<div class=\"col-sm-12\">' + 
            '<div class=\"media\">' + 
                '<div class=\"media-left\">' + 
                    '<img src=\"' + repo.pt_pic + '\" class=\"media-object\" alt=\"User Image\"  style=\"width:70px;\" />' + 
                '</div>' + 
                '<div class=\"media-body\">' + 
                    '<h4 style=\"margin-top: 0\">HN : ' + repo.pt_hn+' $labelName : '+ repo.pt_firstname+' '+repo.pt_lastname + '</h4>' + 
                        '<p>' +
                            '$labelCitizen : ' + repo.pt_cid + ' ' + '$labelBdate : ' + repo.pt_bdate +
                        '</p>' +                        
                        '<p style=\"margin-bottom: 0\">' +
                            fulladdress +
                        '</p>' +
                '</div>' + 
            '</div>' + 
        '</div>' + 
    '</div>';
    return '<div style=\"overflow:hidden;\">' + markup + '</div>';
    };
    var formatRepoSelection = function (repo) {
        var fullname = repo.text;
        if(repo.pt_hn){
            fullname = 'HN : ' + repo.pt_hn+' {$labelName} : '+repo.pt_firstname+' '+repo.pt_lastname
        }
        return fullname;
} ", View::POS_HEAD);

// script to parse the results into the format expected by Select2
    $resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 30) < data.total_count
        }
    };
}
JS;
// render your widget
    echo Select2::widget([
        'name' => 'patient-search',
        'id' => 'patient-search',
        'value' => $dataid,
        'initValueText' => $fullname,
        'options' => ['placeholder' => Yii::t('patient', 'Find patient') . ' ...'],
        'size' => Select2::MEDIUM,
        'pluginOptions' => [
            'allowClear' => FALSE,
            //'minimumInputLength' => 3,
            'ajax' => [
                'url' => Url::to('/patient/patient/search'),
                'dataType' => 'json',
                'delay' => 10,
                'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                'processResults' => new JsExpression($resultsJs),
                'cache' => true
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('formatRepo'),
            'templateSelection' => new JsExpression('formatRepoSelection'),
        ],
    ]);
    ?>
</div>
<?php
$tab = isset($tab) ? $tab : '1';
$url = Url::to(['/patient/patient', 'reloadDiv' => $reloadDiv, 'tab' => $tab]);
$this->registerJs(" 
    function getUiAjax(url, divid) {
           $.ajax({
               method: 'POST',
               url: url,
               dataType: 'HTML',
               success: function(result, textStatus) {
                   $('#'+divid).html(result);
                   }
           });
    }    

    $('#patient-search').on('change', function(e) {
        if($('#patient-search').select2('val')){
            var url = '$url' +'&dataid='+$('#patient-search').select2('val');
            window.location = url;
            //getUiAjax(url, '$reloadDiv');
        }
    });
");
?>
