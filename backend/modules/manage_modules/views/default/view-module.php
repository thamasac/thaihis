<?php
cpn\chanpan\assets\mdi\MDIAsset::register($this);
$imgPath = Yii::getAlias('@storageUrl');
$noImage = $imgPath.'/ezform/img/no_icon.png';
//\appxq\sdii\utils\VarDumper::dump($datas);
$data=[];
foreach($list as $key=>$d){
    $link = \yii\helpers\Url::to(["/ezmodules/ezmodule/view?id={$d['module_id']}"]);
    if(isset($d['ezm_type']) && $d['ezm_type'] == '1'){
        $link = \yii\helpers\Url::to([$d['ezm_link']]);
    }
    $data[$key] = [
        'id'=>$d['id'],
        'data-id'=>$d['module_id'],
        'name'=>$d['module_name'],
        'detail'=>$d['detail'],
        'image'=>$d['image'],
        'icon'=>$d['module_icon'],
        'forder'=>$d['order_by'],
        'mode'=>$d['view_mode'],
        'image_default'=>$d['url_default'],
        'imgPath'=>$imgPath,
        'noImage'=>$noImage,
        'enabledButton'=>false,
        'enabledLink'=>true,
        'link'=> $link,//\yii\helpers\Url::to(["/ezmodules/ezmodule/view?id={$d['module_id']}"]),
        'color'=>$d['color'],
        'module_id'=>$d['module_id']        
        
    ];
} 
//appxq\sdii\utils\VarDumper::dump($data); 
?>
<?php
    $active = (new yii\db\Query())->select('*')->from('zdata_manage_modules')->where('rstat not in(0,3)')->one();
    if(empty($active)) {$active['view_mode'] = 1;}

    if(!empty($active) && $active['view_mode'] == 1){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(FALSE);
    }else if($active['view_mode'] == 2){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(TRUE, '3');
    }else if($active['view_mode'] == 3){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiList();
    }else{
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(FALSE);
    }

?>