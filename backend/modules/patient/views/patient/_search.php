<?php

use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;

$formGroup = $action == 'cpoe' ? 'form-group' : '';
?> 
<div class="<?= $formGroup ?>">      
    <?php
    $formatJs = <<< 'JS'

JS;

// Register the formatting script
    $labelName = Yii::t('patient', 'Name');
    $labelBdate = Yii::t('patient', 'Birthday');
    $labelAge = Yii::t('patient', 'Age');
    $labelCitizen = Yii::t('patient', 'Citizen ID');
    $labelAddress = Yii::t('patient', 'Address');
    $this->registerJs(" 
    function getAge(birthday) {
        var Bdate = birthday.split('/');
        Bdate = Bdate[1] + '/' + Bdate[0] + '/' + (Bdate[2]-543);   
        Bdate = new Date(Bdate);
        var today = new Date();
        var thisYear = 0;          
        if (today.getMonth() < Bdate.getMonth()) {
            thisYear = 1;
        } else if ((today.getMonth() == Bdate.getMonth()) && today.getDate() < Bdate.getDate()) {
            thisYear = 1;
        }
        var age = today.getFullYear() - (Bdate.getFullYear()) - thisYear;
        return age;
    }
    var formatRepo = function (repo) {
    if (!repo.pt_hn) {
        return repo.text;
    }   
    /*var fulladdress = '';
    if(repo.pt_address){
        fulladdress = '<strong>$labelAddress : </strong>' + 
        repo.pt_address +' ม.'+ repo.pt_moi +' ต.'+repo.DISTRICT_NAME +
        ' อ.'+repo.AMPHUR_NAME +' จ.'+repo.PROVINCE_NAME + ' ' + repo.pt_addr_zipcode;
    }*/
    var markup =
    '<div class=\"row\">' +   
        '<div class=\"col-sm-12\">' + 
            '<div class=\"media\">' + 
                '<div class=\"media-left\">' + 
                    '<img src=\"' + repo.pt_pic + '\" class=\"media-object\" alt=\"User Image\"  style=\"width:70px;\" />' + 
                '</div>' + 
                '<div class=\"media-body\">' + 
                    '<div class=\"\" style=\"margin-bottom: 5px;font-size: 17px;\"><strong>HN : ' + repo.pt_hn+'</strong> $labelName : '+ repo.pt_firstname+' '+repo.pt_lastname + '</div>' + 
                        '<div style=\"margin-bottom: 5px;font-size: 15px;\">' +
                            '<strong>$labelCitizen : </strong>' + repo.pt_cid +
                        '</div>' + 
                        '<div style=\"margin-bottom: 5px;font-size: 15px;\">' +
                            '<strong>$labelBdate : </strong>' + repo.pt_bdate + ' <strong>$labelAge : </strong>' + getAge(repo.pt_bdate)+
                        '</div>' +    
                        /*'<div style=\"margin-bottom: 5px;font-size: 15px;\">' +
                            fulladdress +
                        '</div>' +*/
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
          console.log(params);
          console.log(data.total_count);
    params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 10) < data.total_count
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
if ($action == 'reg') {
    $url = Url::to(['/patient/patient', 'reloadDiv' => $reloadDiv, 'tab' => $tab]);
    $jsAddon = "
        var url = '$url' +'&dataid='+$('#patient-search').select2('val');
        window.location = url;
        ";
} elseif ($action == 'cpoe') {
    $url = Url::to(['/cpoe', 'action' => 'search',]);
    $jsAddon = "
        var url = '$url' +'&ptid='+$('#patient-search').select2('val');
        window.location = url;
        ";
} elseif ($action == 'report-checkup') {
    $url = Url::to(['/cpoe/report-checkup', 'action' => 'search',]);
    $jsAddon = "
        var url = '$url' +'&ptid='+$('#patient-search').select2('val');
        window.location = url;
        ";
}

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
           $jsAddon
            //getUiAjax(url, '$reloadDiv');
        }
    });
");
?>
