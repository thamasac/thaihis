<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php if(!empty($sum)):?>
    <div class="row" style="font-size: 18px;">
        <?php foreach ($sum as $key => $value):?>
        <div class="col-md-3">
            <h4><?=$key?></h4>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th><?=Yii::t('app', 'Complete')?></th>
                        <td style="text-align: right;"><code><?=number_format($value['tsum'])?></code></td>
                    </tr>
                    <tr>
                        <th><?=Yii::t('app', 'Failed')?></th>
                        <td style="text-align: right;"><code><?=number_format($value['fsum'])?></code></td>
                    </tr>
                    <tr>
                        <th><?=Yii::t('yii', 'Error')?></th>
                        <td style="text-align: right;"><code><?=number_format($value['esum'])?></code></td>
                    </tr>
                    <tr>
                        <th><?=Yii::t('app', 'Total')?></th>
                        <td style="text-align: right;"><code><?=number_format($value['all'])?></code></td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        
        <?php endforeach;?>
    </div>
  <div class="row" >
      <div class="col-md-12">
        <a href="<?= yii\helpers\Url::to(['/ezbuilder/ezform-builder/update', 'id'=> isset($sum['Ezform']['ezf_id'])?$sum['Ezform']['ezf_id']:0])?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open"></i> <?= Yii::t('ezform', 'Show Form')?></a> 
    </div>
</div>
    
<?php endif;?>