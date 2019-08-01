<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel">Ezform</h4>
    </div>
<div class="modal-body">
    <?php
    if(!empty($forms)){
        $html = '';
        foreach ($forms as $key => $value) {
            if(isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end']) ){
                $dataset = [
                    $value['end'] => $end,
                    $value['start'] => $start,
                    $value['allday'] => $allDay=='true'?1:0,
                ];
                if(isset($value['editable'])){
                    $html .= backend\modules\ezforms2\classes\EzfHelper::btn($value['ezf_id'])
                        ->initdata($dataset)
                        ->label('<i class="glyphicon glyphicon-plus"></i> '.$value['label'])
                        ->options(['class'=>'btn btn-success ezform-main-open', 'style'=>"background-color: {$value['color']}; border-color: {$value['color']};"])    
                        ->buildBtnAdd().' ';
                }
                
                
            }
        }
        echo $html;
    }
    ?>
</div>