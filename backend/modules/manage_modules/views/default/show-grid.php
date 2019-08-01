<?php
cpn\chanpan\assets\mdi\MDIAsset::register($this);
$imgPath = Yii::getAlias('@storageUrl');
$noImage = $imgPath.'/ezform/img/no_icon.png';
//\appxq\sdii\utils\VarDumper::dump($datas);
$data=[];
foreach($datas as $key=>$d){
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
        'enabledButton'=>true,
        'enabledLink'=>false,
        'link'=> \yii\helpers\Url::to(["/ezmodules/ezmodule/view?id={$d['module_id']}"]),
        'color'=>$d['color'],
        'module_id'=>$d['module_id']           
        
    ];
}
?>
<?php 
    $active = (new yii\db\Query())->select('*')->from('zdata_manage_modules')->where('rstat not in(0,3)')->one();
    if(empty($active)) {$active['view_mode'] = 1;}
?>
<div class="row">
    <div class="col-md-12 text-center" style="    border-bottom: 1px solid #e6e6e6;
    padding: 10px;
    border-bottom-style: dashed;">
        <div class="btn-group" role="group" aria-label="...">
            <button type="button" class="btnMode btn btn-default <?= (!empty($active) && $active['view_mode'] == 1) ? 'active' : ''?>" data-id="1"><i class="mdi mdi-view-module"></i> Basic icon</button>
            <button type="button" class="btnMode btn btn-default <?= (!empty($active) && $active['view_mode'] == 2) ? 'active' : ''?>" data-id="2"><i class="mdi mdi-view-grid"></i> Customized icon</button>
            <button type="button" class="btnMode btn btn-default <?= (!empty($active) && $active['view_mode'] == 3) ? 'active' : ''?>" data-id="3"><i class="mdi mdi-code-equal"></i> Full Customized view</button>
       </div>
    </div>
</div>
<br />
<div class="clearfix"></div>
<?php  
    if(!empty($active) && $active['view_mode'] == 1){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(FALSE);
    }else if($active['view_mode'] == 2){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(TRUE);
    }else if($active['view_mode'] == 3){
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiList();
    }else{
        echo backend\modules\manage_modules\components\CNModulesComponent::ui()->setDatas($data)->uiGridBasic(FALSE);
    }

?>

<?php richardfan\widget\JSRegister::begin(); ?>
<script>
      $('.btnMode').on('click', function(){
          let id = $(this).attr('data-id');
          let url = '<?= \yii\helpers\Url::to(['/manage_modules/default/view-mode'])?>';
          $.get(url , {view_mode:id}, function(data){
              if(data['status'] == 'success'){
                  location.reload();
              }
          });
          return false;
      });
</script>
<?php richardfan\widget\JSRegister::end();?>