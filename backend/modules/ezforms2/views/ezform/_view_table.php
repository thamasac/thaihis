<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="ezform-view-table">
    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">View Table</h4>
    </div>
    <div class="modal-body">
        <?php
        echo \appxq\sdii\widgets\GridView::widget([
            'id' => 'generate-sql-grid',
            'dataProvider' => $provider,
        ]);
       ?>
    </div>
</div>