<?php
    use \richardfan\widget\JSRegister;
    $this->title="Permission";
?>

<?= \yii\helpers\Html::radioList("permission2", '', [
    '1'=>Yii::t('ezmodule','Euery one'), //'Euery one',
    '2'=>Yii::t('ezmodule','Restricted'),
],[
    'class'=>'',
    'id'=>'radio_permission',
])?>

<hr/>
<div id="view-permission2" ></div>

<?php JSRegister::begin() ?>
 <script>
        $('input[name=permission2]').on('change', function() {
            let value = $(this).val();
                let ezf_id = '1519034841046921800';
                localStorage.setItem('module_id', '<?= $module_id?>');
                localStorage.setItem('value', '<?= $value?>');
                
                if(value == 2){
                    let url = '<?= yii\helpers\Url::to(['/ezforms2/ezform-data/ezform']) ?>';
                    $.get(url,{ezf_id:ezf_id}, function (data) {
                        $('#view-permission2').html(data);
                        $('.modal-header').hide();
                    });
                } else {
                    $('#view-permission2').html('');
                }
       });
           
 </script>    
<?php JSRegister::end() ?>