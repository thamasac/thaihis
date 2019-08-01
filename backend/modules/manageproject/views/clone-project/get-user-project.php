<?php 
    $imgPath = Yii::getAlias('@storageUrl');
    
    $imgBackend = Yii::getAlias('@backendUrl');
    $imageSec = $imgBackend."/img/health-icon.png";
    $auty_key= \cpn\chanpan\classes\CNUser::GetAuthKey();
    
    $auty_key = backend\modules\manageproject\classes\CNCryptography::EncryptOpenssl($auty_key, "damasac!@#$%");
    
?> 

<?php foreach ($data as $d) { ?>
    <div class="col-xs-3 col-md-2" style="margin-bottom: 20px;">
        <div class="media-left">
            <a  href="https://<?php echo "{$d['projurl']}.{$d['projdomain']}/?auth_key={$auty_key}&status=1"; ?>">
                <div style="margin: 4px;">
                    <?php if(!empty($d['projecticon'])):?>
                     <img 
                         src="<?php echo "{$imgPath}/ezform/fileinput/{$d['projecticon']}"; ?>" 
                         class="img-rounded" 
                         width="72" height="72">
                    <?php else:?>
                     <img 
                         src="<?php echo "{$imageSec}"; ?>" 
                         class="img-rounded" 
                         width="72" height="72">
                    <?php endif; ?>
                </div>
            </a>
            <h4 class="media-heading text-center" style="font-size: 13px;"><strong><?= $d['projectacronym']?></strong></h4>
        </div>

    </div> 

<?php } ?>
