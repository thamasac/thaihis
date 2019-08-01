<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$options = isset($model->ezf_field_options)? appxq\sdii\utils\SDUtility::string2Array($model->ezf_field_options):[];
$special = isset($options['specific'])?$options['specific']:[];



?>
<h1 >Pop-Up help <small style="color: #eb622c;"><?=$model->ezf_field_label?></small></h1>
  <div style="padding: 1px 15px; border-radius: 4px; border: 1px solid #ccc;margin-bottom: 20px;">
      <?php echo (isset($special['popup_help']))?$special['popup_help']:'';?>
  </div>