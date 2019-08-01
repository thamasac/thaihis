<?php
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
\backend\modules\ezforms2\assets\ListdataAsset::register($this);

?>

    <div id="items-side-scroll-<?=$reloadDiv?>" class="row">
        <div class="col-lg-12">
           
            <?php if(isset($options['search']) && !empty($options['search'])) {?>
            <div id="ezf-search-<?=$reloadDiv?>" style="padding: 5px;">
            <?php
            $form = ActiveForm::begin([
                        'id' => 'search-'.$reloadDiv,
                        //'action' => ['index'],
                        'method' => 'get',
            ]);
            ?>
            <?=Html::textInput($options['search'], isset($_GET[$options['search']])?$_GET[$options['search']]:'', ['class'=>'form-control search-query', 'placeholder'=> (isset($options['placeholder']))?$options['placeholder']:Yii::t('ezform', 'Search ...')]);?>
            <?php ActiveForm::end(); ?>
            </div>
            <?php
            $this->registerJs(" 
            $('form#search-{$reloadDiv}').on('change', function(e) {
                $(this).submit();   
            });
            $('form#search-{$reloadDiv}').on('beforeSubmit', function(e) {
                var \$form = $(this);
                $.ajax({
                    method: 'GET',
                    url: \$form.attr('action'),
                    data: \$form.serialize(),
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#{$reloadDiv}').html(result);
                    }
                });
                return false;
            });
            ");
            }
            
            $class_css = isset($options['class_css']) && !empty($options['class_css'])?$options['class_css']:'col-md-12';
            $layout = isset($options['layout']) && !empty($options['layout'])?$options['layout']:'<div class="text-right col-md-12" style="margin-bottom: 5px;">{summary}</div>{items}<div class="list-pager">{pager}</div>';
            $style = isset($options['style']) && !empty($options['style'])?$options['style']:'';
            ?>
            
            <div id="ezf-items-<?=$reloadDiv?>">
                <?=
                ListView::widget([
                    'id'=>'ezf-view-'.$reloadDiv,
                    'options' => ['class'=>'list-box row'],
                    'dataProvider' => $dataProvider,
                    'itemOptions' => ['class' => "list-item $class_css", 'style'=>$style],
                    'layout'=>$layout,
                    'itemView' => function ($model, $key, $index, $widget) use ($sql_builder, $options, $get_params) {
                    
                        $path = [];
                        foreach ($model as $key_var => $valu_var) {
                            $path["{{$key_var}}"] = $valu_var;
                        }
                        
                        if(isset($options['image']) && !empty($options['image']) ){
                            $width = isset($options['image_wigth'])?$options['image_wigth']:64;
                            $url = Yii::getAlias('@storageUrl/ezform/fileinput/');
                            $src = Yii::getAlias('@storageUrl/images/nouser.png');
                            if(isset($path[$options['image']]) && !empty($path[$options['image']])){
                                $src = $url . $path[$options['image']];
                            }
                            $path["{image}"] = Html::img($src, ['class'=>'media-object img-rounded', 'height'=>$width, 'width'=>$width]);

                        }
                        
                        $key_id = '';
                        if(isset($options['key_id']) && !empty($options['key_id']) && isset($path[$options['key_id']])){
                            //$path['{key_id}'] = $path[$options['key_id']];
                            $key_id = $path[$options['key_id']];
                        }
                        
                        
                        $fix_path = $get_params;
                        
                        $path['{image_wigth}'] = $options['image_wigth'];
                        $path['{module}'] = $fix_path['id'];
                        unset($fix_path['id']);
                        unset($fix_path['options']);
                        
                        foreach ($fix_path as $key_get => $value_get) {
                            $path["{{$key_get}}"] = $value_get;
                        }
                        
                        unset($get_params['sql_id']);
                        unset($get_params['reloadDiv']);
                        unset($get_params['options']);
                        $url = ['/ezmodules/ezmodule/view'];
                        
                        if(isset($options['query_params']) && !empty($options['query_params']) ){
                            $query_params = strtr($options['query_params'], $path);
                            $arryq = explode('&', $query_params);
                            if(isset($arryq) && !empty($arryq)){
                                foreach ($arryq as $keyq => $valueq) {
                                    $arryq_var = explode('=', $valueq);
                                    $var_name = isset($arryq_var[0])?$arryq_var[0]:\appxq\sdii\utils\SDUtility::getMillisecTime();
                                    $var_value = isset($arryq_var[1])?$arryq_var[1]:'';
                                    if($var_value=='{key_id}'){
                                        $var_value = $key_id;
                                        
                                        $path['{active}'] = isset($get_params[$var_name]) && $key_id == $get_params[$var_name]?'active':'';
                                    }
                                   
                                    $get_params[$var_name] = $var_value;
                                }
                            }
                        }
                        
                        $path['{url}'] = \yii\helpers\Url::to(\yii\helpers\ArrayHelper::merge($url, $get_params));
                        
                        $template = isset($options['template_content'])?$options['template_content']:'';
                        
                        $content = strtr($template, $path);
                        
                        return $content;
                    },
                ])
                ?>
            </div>
        </div>
    </div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#ezf-view-<?=$reloadDiv?> .pagination a').on('click', function() {
        getAjax($(this).attr('href'));
        return false;
    });
    
    function getAjax(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#<?=$reloadDiv?>').html(result);
            }
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>