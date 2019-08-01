<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<?php \appxq\sdii\widgets\CSSRegister::begin([
    //'key' => 'bootstrap-modal',
    //'position' => []
]); ?>
<style>
    /*CSS script*/
    .table-scroll {
	position:relative;
	overflow:hidden;
    }
    .table-scroll .table-wrap {
            width:100%;
            overflow:auto;
    }
    .table-scroll table {
            width:100%;
            margin:auto;
    }
    .table-scroll th, .table-scroll td {
            white-space:nowrap;
            vertical-align:top;
    }
    .table-scroll thead, .table-scroll tfoot {
            
    }
    .table-scroll .clone {
            position:absolute;
            top:0;
            left:0;
            pointer-events:none;
    }
    .table-scroll .clone th, .table-scroll .clone td {
            visibility:hidden
    }
    .table-scroll .clone td, .table-scroll .clone th {
            border-color:transparent
    }
    .table-scroll .clone tbody th {
            visibility:visible;
    }
    .table-scroll .clone thead .fixed-side {
            border-bottom: 2px solid #ddd !important;
            background-color:#fff;
            visibility:visible;
    }
    .table-scroll .clone tbody .fixed-side {
            border-top: 1px solid #ddd;
            visibility:visible;
    }
    .table-scroll .clone tbody tr:nth-child(odd) .fixed-side {
            background-color:#f9f9f9;
    }
    .table-scroll .clone tbody tr:nth-child(even) .fixed-side {
            background-color:#fff;
    }
    .table-scroll .clone thead, .table-scroll .clone tfoot{background:transparent;}
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>



<button class="btn btn-xs btn-success btn-tb-add"><i class="glyphicon glyphicon-plus"></i></button>

<div class="table-scroll">
  <div class="table-wrap">
      <table class="table table-striped "> 
        <thead> 
          <tr> 
            <th class="fixed-side" style="height: 60px;min-width: 200px;">รายการยา</th> 
            <th class="fixed-side text-center">สถานะ</th> 
            <th class="fixed-side text-right">รวม</th> 
            <th class="" style="height: 60px;" class="text-center">
              10/05/2019 <br>
              <button class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i></button>
              <button class="btn btn-xs btn-danger btn-tb-del"><i class="glyphicon glyphicon-trash"></i></button>
              <button class="btn btn-xs btn-default"><i class="glyphicon glyphicon-print"></i></button>
            </th>
            <th class="text-center">8/05/2019</th>
            <th class="text-center">8/05/2019</th>
            <th class="text-center">8/05/2019</th>
            <th class="text-center">8/05/2019</th>
            <th class="text-center">8/05/2019</th>
          </tr> 
        </thead> 
        <tbody>
          <tr> 
            <td class="fixed-side">Mark<br>dsfdf</td> 
            <td class="fixed-side text-center">Y</td> 
            <td class="fixed-side text-right">10</td> 
            <td class=" text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
          </tr>
          <tr> 
            <td class="fixed-side">Jacob</td> 
            <td class="fixed-side text-center">N</td> 
            <td class="fixed-side text-right">20</td> 
            <td class=" text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
          </tr> 
          <tr> 
            <td class="fixed-side">Larry</td> 
            <td class="fixed-side text-center">Y</td> 
            <td class="fixed-side text-right">30</td> 
            <td class=" text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
            <td class="text-center">1</td>
          </tr> 
        </tbody>
      </table>
  </div>
</div>



<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    jQuery("#<?=$reloadDiv?> .table-scroll table").clone(true).appendTo('#<?=$reloadDiv?> .table-scroll').addClass('clone');

    $('.btn-tb-del').on('click', function(){
        let col = $(this).parent().index()+1;
        $('#<?=$reloadDiv?> .table-scroll table th:nth-child('+col+')').remove();
        $('#<?=$reloadDiv?> .table-scroll table td:nth-child('+col+')').remove();
    });
    
    $('.btn-tb-add').click(function(){
        $('#<?=$reloadDiv?> .table-scroll table th:nth-child(3)').after('<th class="text-center">2/05/2019 <br> <button class="btn btn-xs btn-danger btn-tb-del"><i class="glyphicon glyphicon-trash"></i></button></th>');
        $('#<?=$reloadDiv?> .table-scroll table td:nth-child(3)').after('<td class="text-center">2</td>');
    });
    
    //scroll loader x
    $('#<?=$reloadDiv?> .table-scroll .table-wrap').on('scroll', function() {
        let div = $(this).get(0);
        if(div.scrollLeft + div.clientWidth >= div.scrollWidth) {
            // do the lazy loading here
            $('#<?=$reloadDiv?> .table-scroll table thead tr').append('<th class="text-center">8/05/2019</th>');
            
            $('#<?=$reloadDiv?> .table-scroll table tbody tr').append('<td class="text-center">1</td>');
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>