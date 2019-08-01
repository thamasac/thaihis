<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$attibuteMap = ['citizenId',
    'birthdate',
    'sex',
    'thPrefix',
    'thFirstname',
    'thLastname',
    'enPrefix',
    'enFirstname',
    'enLastname',
    'issueDate',
    'expireDate',
    'address',
    'addrHouseNo',
    'addrVillageNo',
    'addrExtra',
    'addrLane',
    'addrRoad',
    'addrTambol',
    'addrAmphur',
    'addrProvince',
    'photo'
];

// TODO redirect to and map field
$getFieldUrl = Url::to(['/ezforms2/target/get-fields']);

// CONFIRM BOX
?>
    <div class="form-group">
        <?php
        echo kartik\select2\Select2::widget([
            'name' => 'options[action]',
            'value' => isset($options['action']) ? $options['action'] : 'REDIRECT',
            'options' => ['placeholder' => Yii::t('ezform', 'ACTION'), 'class' => 'col-md-10', 'id' => 'config_action_type'],
            'data' => ["REDIRECT" => "REDIRECT", "MODAL" => "MODAL"],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
<?php
$redirectLabel = Html::label("Redirect URL", 'redirect-url-input', ['class' => 'control-label']);
$redirectInput = HTML::input('text', 'options[redirectUrl]', $options['redirectUrl'], ['id' => 'redirect-url-input', 'class' => 'form-control']);
$badgeInputs = "";
for ($i = 0; $i < count($attibuteMap) - 1; $i++) {
    $attr = $attibuteMap[$i];
    $badgeInputs .= "<span style='margin-right: 4px' class='badge badge-secondary'>$attr</span>";
}
echo HTML::tag('div', "<p> PARAM " . $badgeInputs . "</p>" . $redirectLabel . $redirectInput, ['id' => 'redirect-panel']);

?>
    <div id="ezform-panel">
        <div id="ezform-panel-body">
            <div class="form-group">
                <?php
                $ezforms = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll("");
                echo kartik\select2\Select2::widget([
                    'name' => 'options[ezf_id]',
                    'value' => $options['ezf_id'],
                    'options' => ['placeholder' => Yii::t('ezform', 'EZFORM'), 'class' => 'col-md-10', 'id' => 'config_ezf_id'],
                    'data' => ArrayHelper::map($ezforms, 'ezf_id', 'ezf_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="progress" id="loading-config-reader">
                <div id class="progress-bar progress-bar-striped active" role="progressbar"
                     aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                    Loading EzFields
                </div>
            </div>
            <div id="div-field-list">
                <?php
                foreach ($attibuteMap as $value) {
                    ?>
                    <div class="col-md-12 sdbox-col">
                        <?php
                        $value_fields = json_encode($options['map']);
                        ?>
                        <?= Html::label("Map $value with fields", "ref_field_box_$value", ['class' => 'control-label']) ?>
                        <div class="ref_field_box" id="ref_field_box_<?= $value ?>" data-att="<?= $value ?>">

                        </div>

                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <p>.</p>

<?php
$noty = \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"');
$this->registerJS(<<<JS


function updateView(action = null){
    if(action == null)
    action = $('#config_action_type').val();;
    switch (action) {
      case 'REDIRECT':
          $('#ezform-panel-body').hide();
          $('#redirect-panel').show();
          break;
      case 'MODAL':
          $('#ezform-panel-body').show();
          $('#redirect-panel').hide();
          break;
    }
}


$('#config_action_type').on('change', function () {
    let action = $(this).val();
    console.log('CHANGED',action);
    updateView(action);
});
updateView();

$('#config_ezf_id').on('change', function () {
    let ezf_id = $(this).val();
    console.log('CHANGED',ezf_id);
    fields(ezf_id);
});

function fields(ezf_id) {
    $("#loading-config-reader").show();
    $("#div-field-list").hide();
    let valueField = $value_fields;
        // let value = valueField ? valueField[attr] : null;
        $.post('$getFieldUrl', {
                ezf_id: ezf_id,
                multiple: 1,
                id: 'config_fields_REPLACETHISATTR',
                name: "options[map][REPLACETHISATTR]",
            }
        ).done(function (result) {
            $("#loading-config-reader").hide();
            $("#div-field-list").show();
            $('.ref_field_box').each(function () {
                let temp = result;
                let attr =  $(this).attr('data-att');
                let value = valueField ? valueField[attr] : null;

                temp = temp.replace(/REPLACETHISATTR/g,attr)
                  $(this).html(temp);
                $('#config_fields_'+attr).val(value).trigger('change');
            });
        }).fail(function () {
            $noty
                $("#loading-config-reader").hide();
            console.log('server error');
        });
}
    fields($('#config_ezf_id').val());

JS
);
//$value_fields