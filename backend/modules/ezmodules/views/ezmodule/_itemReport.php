<?php
	
	$percen = number_format(($num/$count)*100,2);
	
	?>
<tr> 
    <td>&nbsp; &nbsp; &nbsp; <?=$label?></td> 
    <td style="width: 200px; text-align: right;"><button class="btn btn-success btn-xs btn-views" data-url="" ><?=number_format($num)?></button></td> 
    <td style="width: 200px; text-align: right;"><?=$percen?>%</td>
    <td style="width: 200px; text-align: right;">
	<div class="progress">
	<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?=$percen?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$percen?>%;">
	  
	</div>
      </div>
    </td> 
</tr> 